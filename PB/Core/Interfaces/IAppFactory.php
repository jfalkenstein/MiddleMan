<?php

namespace PB\Core\Interfaces;

/**
 * Provides centralized access to the various services of the application.
 * @author jfalkenstein
 */
interface IAppFactory {
    /**
     * @return IRegistrationService
     */
    public function RegistrationService();
    /**
     * @return IRequestFactory
     */
    public function RequestFactory();
    /**
     * @return IResponseFactory
     */
    public function ResponseFactory();
    /**
     * @return IEventManager 
     */
    public function EventManager();
    /**
     * @return IAuthenticator
     */
    public function Authenticator();
    /**
     * @return IResponseSerializer
     */
    public function ResponseSerializer();
    /**
     * @return IModuleManager
     */
    public function ModuleManager();
    /**
     * @return IConfigManager
     */
    public function ConfigManager();
    
    /**
     * @param string $dependencyName The name of the dependency desired.
     * @return Instance of dependency requested
     */
    public function GetOther($dependencyName);
}
