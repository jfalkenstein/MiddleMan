<?php

namespace PB\Core\Application;

use PB\Core\Events\Event;
use PB\Core\Exceptions\ApplicationException;
use PB\Core\Exceptions\PbException;
use PB\Core\Interfaces\IApplication;
use PB\Core\Interfaces\IEventManager;
use PB\Core\Interfaces\IManager;
use PB\Core\Interfaces\ISubscriber;

/**
 * This is the main controlling class of the middleman application.
 * It runs the whole lifecycle from request to response by means of an event notification
 * system. For the Event Notification System, see @link Events::EventManager EventManager @endlink.
 * The @link Events::Event Event @endlink that is passed arround accumulates 
 * all the various information through the lifecycle.
 * @author jfalkenstein
 */
class Application implements IApplication, ISubscriber{    
    private $manager; /**< The Interfaces::IManager to be utilized 
     * throughout the application lifecycle.
     */
    
    /**
     * This is part of the ISubscriber implementation. It provides the event subscriptions
     * along with their accompanying methods to call when the event fires. It essentially
     * is the basic wiring that holds the entire application together. Whenever an event is
     * fired (usually triggered by the @link Manager::Manager Manager @endlink),
     * if it is subscribed to here, it the corresponding method call will be made.
     * @return array An associative array of subscribed events associated with methods to
     * invoke when those events are fired.
     */
    public function getSubscribedEvents() {
        return [
            'requestProcessed' => 'onRequestProcessed',
            'authenticationFail' => 'onAuthenticationFail',
            'authenticationPass' => 'onAuthenticationPass',
            'readyForOutput' => 'onReadyForOutput',
            'moduleSet' => 'onModuleSet',
            'moduleLoaded' => 'onModuleLoaded',
            'moduleExecuted' => 'onModuleExecuted',
            'responseReady' => 'onResponseReady',
        ];
    }
    /**
     * Upon construction, Application subscribes itself to the eventmanager.
     */
    public function __construct(IManager $manager, IEventManager $eventManager) {
        $this->manager = $manager;
        $eventManager->addSubscriber($this);
    }
    /**
     * This initiates the lifecycle, beginning with processing the request. It also
     * provides global exception handling, so that any exceptions encountered within
     * the request lifecycle are output in a standardized format via the @link Interfaces::IResponseSerializerserializer
     * response serializer @endlink.
     * 
     * Because calling Manager::Manager::processRequest() initiates the
     * lifecylcle and a series of event calls that string the application together,
     * using the try/catch block on it amounts to near-global exception handling.
     * Any exceptions thrown any later in the application lifecycle will bubble up
     * to this function call and will be caught and dealt with.
     * @see Manager::Manager::processRequest()
     * @see Manager::Manager::handleException()
     */
    public function run() {
        //First, instantiate the Event.
        $event = new Event();
        try{
            //Process the event.
            $this->manager->processRequest($event);
        } catch (PbException $ex) { //IF it is a PbException, it has been intentionally thrown.
            $event->setException($ex);
            $this->manager->handleException($event);
        //If it is NOT a pbException, it was thrown due to an encountered
        //exception in the code and needs ti be handked differently to prevent
        //critical info about the server being leaked through the exception.        
        } catch(\Exception $ex){ 
            $newExc = new ApplicationException("The Server encountered an unhandled exception.",0, $ex);
            $event->setException($newExc);
            $this->manager->handleException($event);
        }
    }
    /**
     * Subscribes to: **requestProcessed**
     * @param Event $event
     * @see Manager::Manager::authenticate()
     */
    public function onRequestProcessed(Event $event){
        $this->manager->authenticate($event);
    }
    /**
     * Subscribes to: **authenticationPass**
     * @param Event $event
     * @see Manager::Manager::setModule()
     */
    public function onAuthenticationPass(Event $event){
       $this->manager->setModule($event);
    }
    /**
     * Subscribes to: **authenticationFail**
     * @param Event $event
     * @see Manager::Manager::handleAuthenticationFail()
     */
    public function onAuthenticationFail(Event $event){
        $this->manager->handleAuthenticationFail($event);
    }
    /**
     * Subscribes to: **responseReady**
     * @param Event $event
     * @see Manager::Manager::serializeResponse()
     */
    public function onResponseReady(Event $event){
        $this->manager->serializeResponse($event);
    }
    /**
     * Subscribes to: **moduleSet**
     * @param Event $event
     * @see Manager::Manager::loadModule()
     */
    public function onModuleSet(Event $event){
        $this->manager->loadModule($event);
    }
    
    /**
     * Subscribes to: **readyForOutput**
     * 
     * This actually outputs the serialized response to the client. 
     * @param Event $event
     */
    public function onReadyForOutput(Event $event){
        echo $event->getSerializedResponse();
    }
    /**
     * Subscribes to: **moduleLoaded**
     * @param Event $event
     * @see Manager::Manager::executeModule()
     */
    public function onModuleLoaded(Event $event){
        $this->manager->executeModule($event);
    }
    /**
     * Subscribes to: **moduleExecuted**
     * @param Event $event
     * @see Manager::Manager::createSuccessResponse()
     */
    public function onModuleExecuted(Event $event){
        $this->manager->createSuccessResponse($event);
    }

}
