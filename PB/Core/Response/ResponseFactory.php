<?php

namespace PB\Core\Response;

use PB\Core\Enumerations\ResponseType;
use PB\Core\Events\Event;
use PB\Core\Interfaces\IResponseFactory;

/**
 * Responsible for taking the various data points accumulated in the Events::Event
 * and processing them into a Response object that will be serialized and returned
 * to the client.
 *
 * @author jfalkenstein
 */
class ResponseFactory implements IResponseFactory{
   
    /**
     * Receives an event object, response type, and message and processes it into
     * a Response object.
     * @param Event $event
     * @param int|string $responseType The Enumerations::ResponseType constant 
     * describes this response.
     * @param string $message An additional message that describes this response.
     * @return Response
     */
    public function packageResponse(Event $event, $responseType, $message = null) {
        //Create the new Response object.
        $response = new Response();
        //Obtain the name of the ResponseType constant passed in. Set it to the Response
        $responseTypeName = ResponseType::getName($responseType);
        $response->responseType = $responseTypeName;
        //If the responseType is Exception, set the data property on the response to the actual exception name.
        if(constant(ResponseType::class . '::' . $responseTypeName) === ResponseType::Exception){
            $response->data = get_class($event->getException());
        }else{//Else...
            //Set the data property on the response to data object returned by the module execution.
            $response->data = $event->getData();
        }
        //If the module has been set on the event, set the module and domain on the response.
        if(!is_null($event->getModule())){
            $response->domain = $event->getModule()->getDomain();
            $response->module = $event->getModule()->getName();
        }
        //Set the message
        $response->message = $message;
        //Set the serverToken on the response from the event.
        $response->serverToken = $event->getToken();
        //Set the returnVals on the response from the request's returnVals.
        $response->returnVals = $event->getRequest()->ReturnVals;
        return $response;
    }
}
