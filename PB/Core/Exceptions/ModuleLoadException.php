<?php

namespace PB\Core\Exceptions;

/**
 * An exception encountered at the point of module loading.
 *
 * @author jfalkenstein
 */
class ModuleLoadException extends PbException{
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
