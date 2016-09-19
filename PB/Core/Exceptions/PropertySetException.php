<?php

namespace PB\Core\Exceptions;

/**
 * An exception encounted in the process of setting a property.
 *
 * @author jfalkenstein
 */
class PropertySetException extends PbException{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
