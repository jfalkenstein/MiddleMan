<?php

namespace PB\Core\Exceptions;

/**
 * A global-level exception that has been encountered and handled by the Application class.
 *
 * @author jfalkenstein
 */
class ApplicationException extends PbException {
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
