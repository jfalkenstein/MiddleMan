<?php

namespace PB\Core\AppFactory;

use PB\Core\Interfaces\IAppFactory;
use PB\Core\Interfaces\IAuthenticator;
use PB\Core\Interfaces\IConfigManager;
use PB\Core\Interfaces\IContainer;
use PB\Core\Interfaces\IEventManager;
use PB\Core\Interfaces\IModuleManager;
use PB\Core\Interfaces\IRequestFactory;
use PB\Core\Interfaces\IResponseFactory;
use PB\Core\Interfaces\IResponseSerializer;

/**
 * The AppFactory provides centralized access to the Dependency Injection container.
 * Thus, for all the various services, the AppFactory can provide them as requested.
 * @author jfalkenstein
 */
class AppFactory implements IAppFactory {
    private $container;
    
    public function __construct(IContainer $container) {
        $this->container = $container;
    }
    /**
     * 
     * @return Interfaces::IAuthenticator
     */
    public function Authenticator() {
        return $this->container->get(IAuthenticator::class);
    }
    
    /**
     * 
     * @return Interfaces::IConfigManager
     */
    public function ConfigManager() {
        return $this->container->get(IConfigManager::class);
    }
    /**
     * 
     * @return Interfaces::IEventManager
     */
    public function EventManager() {
        return $this->container->get(IEventManager::class);
    }
    /**
     * 
     * @return Interfaces::IModuleManager
     */
    public function ModuleManager() {
        return $this->container->get(IModuleManager::class);
    }
    /**
     * 
     * @return Interfaces::IRegistrationService
     */
    public function RegistrationService() {
        return $this->container->getRegistrationService();
    }
    /**
     * 
     * @return Interfaces::IRequestFactory
     */
    public function RequestFactory() {
        return $this->container->get(IRequestFactory::class);
    }
    /**
     * 
     * @return Interfaces::IResponseFactory
     */
    public function ResponseFactory() {
        return $this->container->get(IResponseFactory::class);
    }
    /**
     * 
     * @return Interfaces::IResponseSerializer
     */
    public function ResponseSerializer() {
        return $this->container->get(IResponseSerializer::class);
    }
    /**
     * @param string $dependencyName
     * @return The dependency requested.
     */
    public function GetOther($dependencyName) {
        return $this->container->get($dependencyName);
    }

}
