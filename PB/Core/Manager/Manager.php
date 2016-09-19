<?php

namespace PB\Core\Manager;

use PB\Core\Enumerations\ResponseType;
use PB\Core\Events\Event;
use PB\Core\Interfaces\IAppFactory;
use PB\Core\Interfaces\IManager;
use PB\Core\Interfaces\IPublisher;

/**
 * Manager knows how to access all the various core services in order to process
 * the various actions requested by Application::Application. As such, it receives
 * the Interfaces::IAppFactory in its construction and stores that reference so that
 * all the services it may need are accessible through it.
 * 
 * None of the Manager's functions directly return results. Rather, the Manager
 * will call the appropriate event through the EventManager, and thus enable the 
 * subscriber(s) to those events to response accordingly. In practice, the sole subscriber
 * would be the Application::Application object. However, it is possible that a module
 * could subscribe to any of those events.
 *
 * @author jfalkenstein
 */
class Manager implements IManager, IPublisher{
    private $appFactory; /**<The Interfaces::IAppFactory used by the manager
     * to obtain the various services necessary for its actions.
     */
    private $eventManager;/**<The Interfaces::IEventManager used to publish
     * events.
     */
    
    public function __construct(IAppFactory $appFactory) 
    {
        $this->appFactory = $appFactory;
        $this->eventManager = $this->appFactory->EventManager();
    }
    
    /**
     * Authenticates the request. It takes the request $event
     * and uses the Interfaces::IAuthenticator to check for authentication.
     * 
     * *    If the request authenticates, it will assign the response token to
     * $event.
     * 
     * ####Fires events:
     * *    **authenticationFail** if the request cannot be authenticated
     * *    **authenticationPass** if the request passes authentication
     * 
     * @param Event $event
     * @see Authentication::MD5Authenticator::authenticate()
     */
    public function authenticate(Event $event) {
        $req = $event->getRequest();
        $token = $this->appFactory->Authenticator()->authenticate($req);
        if($token === false){
            $this->notify('authenticationFail', $event);
            return;
        }
        $event->setToken($token);
        $this->notify('authenticationPass', $event);
    }
    
    /**
     * Once the module has been set and loaded, this executes the module
     * by means of the ModuleManager::ModuleManager and assigns the returned
     * data object to $event.
     * 
     * ####Fires events:
     * *    **moduleExecuted** once the data object is received and assigned.
     * @param Event $event
     * @see ModuleManager::ModuleManager::executeModule()
     */
    public function executeModule(Event $event) {
        $data = $this->appFactory->ModuleManager()->executeModule(
                $event->getModule(), 
                $event->getRequest(), 
                $this->appFactory);
        $event->setData($data);
        $this->notify('moduleExecuted', $event);
    }
    
    /**
     * Uses the Interfaces::IResponseSerializer to create a serialize string
     * version of the response.
     * 
     * ####Fires events:
     * *    **readyForOutput** once the serializer has created the string and
     * it has been assigned to $event.
     * 
     * @param Event $event
     * @see Response::JsonPSerializer::Serialize().
     */
    public function serializeResponse(Event $event) {
        $request = $event->getRequest();
        $response = $event->getResponse();
        $string = $this->appFactory->ResponseSerializer()->Serialize($request, $response);
        $event->setSerializedResponse($string);
        $this->notify('readyForOutput', $event);
    }
    
    /**
     * Creates the Request::Request object with the Interfaces::IRequestFactory.
     * 
     * ####Fires events:
     * *    **requestProcessed** once the request object has been created and assigned to $event.
     * 
     * @param Event $event
     * @see Request::RequestFactory::packageRequest();
     */
    public function processRequest(Event $event) {
        $req = $this->appFactory->RequestFactory()->packageRequest();
        $event->setRequest($req);
        $this->notify('requestProcessed', $event);  
    }
    
    /**
     * Obtains the requested module and assigns it to $event with the Interfaces::IModuleManager.
     * 
     * ####Fires events:
     * *    **moduleSet** once the module has been instantiated and assigned to $event.
     * 
     * @param Event $event
     * @see ModuleManager::ModuleManager::getModule().
     */
    public function setModule(Event $event) {
        $domain = $event->getRequest()->Domain;
        $moduleName = $event->getRequest()->Module;
        $module = $this->appFactory->ModuleManager()->getModule($domain, $moduleName);
        $event->setModule($module);
        $this->notify('moduleSet', $event);
    }

    /**
     * Publishes an event.
     * @param string $eventName The name of the event to publish.
     * @param Event $event The event object to pass with the event.
     */
    public function notify($eventName, Event $event) {
        $this->eventManager->dispatch($eventName, $event);
    }
    
    /**
     * Creates a Response::Response object specifically for an exception using the
     * Interfaces::IResponseFactory.
     * 
     * ####Fires events:
     * *    **responseReady**
     * 
     * @param Event $event
     * @see Response::ResponseFactory::packageResponse()
     */
    public function handleException(Event $event) {
        $response = $this->appFactory->ResponseFactory()->packageResponse(
                $event, 
                ResponseType::Exception, 
                $event->getException()->getMessage());
        $event->setResponse($response);
        $this->notify('responseReady', $event);
    }
    
    /**
     * Creates a Response::Response object specifically for authentication failures.
     * @param Event $event
     * 
     * ####Fires events:
     * *    **responseReady**
     * 
     * @param Event $event
     * @see Response::ResponseFactory::packageResponse()
     */
    public function handleAuthenticationFail(Event $event){
        $response = $this->appFactory->ResponseFactory()->packageResponse(
                $event, 
                ResponseType::Authentication, 
                'Authorization failed. Invalid token.');
        $event->setResponse($response);
        $this->notify('responseReady', $event);
    }
    
    /**
     * Initializes the obtained module by obtaining configurations, registering
     * dependencies, and executing any initialization code the module might have.
     * It does this with the Interfaces::IModuleManager
     * 
     * ####Fires events:
     * *    **moduleLoaded** Once the module has been initialized.
     * 
     * @param Event $event
     * @see ModuleManager::ModuleManager::loadModule().
     */
    public function loadModule(Event $event) {
        $module = $event->getModule();
        $this->appFactory->ModuleManager()->loadModule($this->appFactory, $module);
        $this->notify('moduleLoaded', $event);
    }

    /**
     * Creates a Response::Response object specifically for successful module execution
     * and assigns it to $event. It uses the Interfaces::IResponseFactory to do this.
     * 
     * ####Fires events:
     * *    **responseReady** Once the response object is created and assigned to $event.
     * 
     * @param Event $event
     * @see Response::ResponseFactory::packageResponse();
     */
    public function createSuccessResponse(Event $event) {
        $response = 
                $this
                ->appFactory
                ->ResponseFactory()
                ->packageResponse($event, ResponseType::Success);
        $event->setResponse($response);
        $this->notify('responseReady', $event);
    }

}
