<?php

namespace PB\Core\Interfaces;

use PB\Core\Events\Event;
use PB\Core\Request\Request;
use PB\Core\Response\Response;

/**
 * Obtains the Request::Request and Response::Response, then serializes the response for
 * output.
 * @author jfalkenstein
 */
interface IResponseSerializer {
    /**
     * @return string The serialized response.
     */
    public function Serialize(Request $request, Response $response);
}
