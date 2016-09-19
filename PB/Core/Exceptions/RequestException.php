<?php

namespace PB\Core\Exceptions;

/**
 * An exception encountered in the process of processing the Request.
 *
 * @author jfalkenstein
 */
class RequestException extends PbException{
    public function __construct($message = "", \Exception $previous = null) {
        $code = 0;
        parent::__construct($message, $code, $previous);
    }
}
