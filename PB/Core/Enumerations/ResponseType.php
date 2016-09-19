<?php

namespace PB\Core\Enumerations;

/**
 * The basic types of responses. This is used in building the response.
 * @author jfalkenstein
 */
class ResponseType extends Enum {
    const Exception = 0;
    const Authentication = 1;
    const Success = 2;
}