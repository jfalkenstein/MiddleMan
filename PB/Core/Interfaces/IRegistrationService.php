<?php

namespace PB\Core\Interfaces;

/**
 *
 * @author jfalkenstein
 */
interface IRegistrationService {
    /**
     * Define an object or a value in the container.
     *
     * @param string                 $name  Entry name
     * @param mixed|DefinitionHelper $value Value, use definition helpers to define objects
     */
    public function set($name, $value);
}
