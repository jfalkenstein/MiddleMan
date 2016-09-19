<?php

namespace PB\Core\Interfaces;

use PB\Core\Events\Event;

/**
 * This creates the Response::Response object, which will be later serialized
 * and sent back to the client.
 * @author jfalkenstein
 */
interface IResponseFactory {
    /**
     * 
     * @param Event $event The event with all the accumulated data up to this point.
     * @param int $responseType The Enumerations::ResponseType constant that describes the type of this response.
     * @param string $message Any desired message to accompany the response 
     * (usually in the case of an exception or other failure).
     */
    public function packageResponse(Event $event, $responseType, $message = null);
}
