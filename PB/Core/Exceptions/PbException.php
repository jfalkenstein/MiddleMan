<?php

namespace PB\Core\Exceptions;

/**
 * This is the parent class of all PB exceptions. The reason for this  class is so
 * that it can be caught at the application level and output. If a PbException is
 * caught, it indicates that the exception was intentionally raised by the application
 * and it does not contain any sensitive information--it is able to be sent to the
 * client.
 *
 * @author jfalkenstein
 */
abstract class PbException extends \Exception {
    public function __construct($message = "", $code = 0, \Exception $previous = null) {
        parent::__construct($message, $code, $previous);
    }
}
