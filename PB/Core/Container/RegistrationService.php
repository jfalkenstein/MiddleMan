<?php

namespace PB\Core\Container;

use PB\Core\Interfaces\IContainer;
use PB\Core\Interfaces\IRegistrationService;
use function DI\object;

/**
 * Provides the ability to register dependencies after bootstrap into the container
 * without otherwise having access to the container. Thus, the container cannot be otherwise
 * modified and services cannot be otherwise obtained, but dependencies can be
 * registered.
 *
 * @author jfalkenstein
 */
class RegistrationService  implements IRegistrationService{
    private $container;
    
    public function __construct(IContainer $container) {
        $this->container = $container;
    }

    /**
     * Define an object or a value in the container for later instantiation.
     *
     * @param string $name  The interface/abstraction name. 
     * @param string $value The class name to map to the interface/abstraction.
     */
    public function set($name, $value) {
        if(is_string($value) && class_exists($value)){
            $value = object($value);
        }
        $this->container->set($name, $value);
    }

}
