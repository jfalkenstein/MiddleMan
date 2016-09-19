<?php

namespace PB\Core\Interfaces;

use PB\Core\Events\Event;

/**
 * The %IManager knows how to use the various services to accomplish the core processes
 * of MiddleMan. In contrast with IApplication, which only knows how to direct the lifecycle
 * and how to use IManager, IManager actually knows how to use all the various core services
 * to move the request through to the response.
 * 
 * Every function takes an Events::Event for a parameter. For further explanation as 
 * to how that functions, see the class definition for that.
 * @author jfalkenstein
 */
interface IManager {
    /**
     * Receives the raw data from the SuperGlobals and parses it into a Request::Request
     * object that can be assigned to the Event.
     */
    public function processRequest(Event $event);
    /**
     * Takes the request from the event and authenticates it.
     */
    public function authenticate(Event $event);
    /**
     * Obtains the module requested and assigns it to the event.
     */
    public function setModule(Event $event);
    /**
     * Executes the module and assigns the result to the event.
     */
    public function executeModule(Event $event);
    /**
     * Receives the Response::Response object from the event and serializes it with
     * the IResponseSerializer, then assigns that string to the event.
     */
    public function serializeResponse(Event $event);
    /**
     * Receives an exception from the event and prepares it for sending as a response.
     */
    public function handleException(Event $event);
    /**
     * In the event the request was not authenticated, this receives the failed
     * request from the event and prepares it to be returned in a response.
     */    
    public function handleAuthenticationFail(Event $event);
    /**
     * Instantiates the module, registering its dependencies and inializing it, as it
     * is programmed.
     */
    public function loadModule(Event $event);
    /**
     * Once the event has moved throught the lifecycle to the point where it has successfully
     * executed the module and recevied the data, it will create the Response::Response from it.
     * @param Event $event
     */
    public function createSuccessResponse(Event $event);
}
