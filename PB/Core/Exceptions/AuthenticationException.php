<?php

namespace PB\Core\Exceptions;

/**
 * An exception encounted during authentication.
 *
 * @author jfalkenstein
 */
class AuthenticationException extends PbException{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
