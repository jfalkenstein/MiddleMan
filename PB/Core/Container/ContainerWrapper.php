<?php

namespace PB\Core\Container;

use DI\Container;
use PB\Core\Interfaces\IContainer;
use PB\Core\Interfaces\IRegistrationService;

/**
 * This is used to wrap up the PHP-DI container. The benefit of this is that none
 * of the core code in this application depends outside itself. All dependency
 * requests are directed toward this class. It functions largely as a facade in
 * front of the actual DI container, though it does institute it's own simple cache
 * to enable faster resolution on services.
 * 
 * The primary function of a DI container is to provide services on request as they
 * are registered by the diDefinitions.php file. Thus (for example) there only ever is one configManager
 * that is provided to other classes that need it. Any changes made to the config array
 * within it are immediately accessible by other classes that are referencing the config
 * manager.
 * 
 * For more information about how dependency injection works in MiddleMan, see 
 * @ref md_documentation_dependencyInjection.
 * 
 * Most of these functions simply alias the encapsulated container's functions.
 * For more detailed guides on usage, see http://php-di.org/doc/container.html.
 * 
 * @author jfalkenstein
 */
class ContainerWrapper implements IContainer {
    
    private $container; /**< The PHP-DI %Container that is encapsulated and 
     * utilized by this class.
     */
    private $cache = [];/**< The associateive array used to cache services that are
     * frequently requested. This is used so that, if the service has already been
     * requested, PHP-DI doesn't have to execute any code (which can be lengthy)
     * to retrieve it.
     * 
     * The associations are between interface name and instantiated object.
     */
    
    /**
     * Upon construction, encapsulate the PHP-DI %Container
     */
    public function __construct(Container $container) {
        $this->container = $container;
    }
    
    /**
     * Will call a given callable with the given parameters, injecting any registered
     * dependicies needed for that callable.
     * 
     * This is not used anywhere within MiddleMan's core code, but is available to
     * any modules that might desire to use it.
     * @param callable $callable The function to call.
     * @param array $parameters The parameters to pass into the function.
     */
    public function call($callable, array $parameters = array()) {
        $this->container->call($callable,$parameters);
    }
    
    /**
     * Will obtain an instance of a dependency identified by its associated interface/abstraction
     * @param string $name
     * @return object
     */
    public function get($name) {
        /*Checks to see if the $name variable (URL) is not set in the cache.*/
        if(!isset($this->cache[$name])){
            /*If URL is not set in the cache, add it.*/
            $this->cache[$name] = $this->container->get($name);
        }
        return  $this->cache[$name];
    }
    /**
     * Will indicate whether or not the di container has a definition for the specified
     * interface/abstraction.
     * @param string $name
     * @return boolean
     */
    public function has($name) {
        return $this->container->has($name);
    }
    
    /**
     * This will inject dependencies on a pre-existing instance and return it.
     * 
     * This is not used in MiddleMan's core code, but it is available to modules that desire
     * to use it.
     * @param $instance
     * @return object
     */
    public function injectOn($instance) {
        return $this->container->injectOn($instance);
    }
    
    /**
     * Even if an object has already been cached since its original instantiation,
     * this will create and return a new one.
     * @param string $name The class name
     * @param array $parameters parameters to pass in for instantiation.
     * @return object
     */
    public function make($name, array $parameters = array()) {
        return $this->container->make($name,$parameters);
    }
    /**
     * Even after the container is already built, this will set new dependencies
     * that can be accessed later.
     * @param string $name The name of the interface/abstraction to be mapped.
     * @param \DI\Definition\Helper\DefinitionHelper $value The definition to be set for that interface.
     */
    public function set($name, $value) {
        $this->container->set($name, $value);
    }
    
    /**
     * This returns a new RegistrationService, that can be used for modules to register
     * their dependencies after bootstrap.
     * @return RegistrationService
     */
    public function getRegistrationService() {
        return new RegistrationService($this);
    }

}
