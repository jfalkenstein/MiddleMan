<?php

namespace PB\Core\Interfaces;

/**
 * This is one of the core classes used for the module system. An IModule is responsible
 * for providing all dependency registrations and configuration values, and then finally
 * to provide an IExecutable object that can perform the requested action.
 * 
 * For direction in creating modules, see @ref md_documentation_modules.
 * 
 * @see ModuleManager::Module for the default implementation of this interface.
 * @author jfalkenstein
 */
interface IModule {
    /**
     * @return string The name of the module
     */
    public function getName();
    /**
     * @return string The domain of the module
     */
    public function getDomain();
    /**
     * A module should register their dependencies with this function.
     * @param IRegistrationService $regService The registration service that enables
     * modules to register their dependencies for later access.
     */
    public function registerDependencies(IRegistrationService $regService);
    /**
     * This will provide a configuration array to the configManager.
     * @return array An associative array of key/value pairs to be merged into 
     * the configManager's cached config array. This enables configurations to be
     * accessed in other files by this module.
     */
    public function getConfigs();
    /**
     * It injects the IAppFactory so that any further setup/execution to prepare for the module's 
     * functioning are completed and that any application services are accessible.
     * @param IAppFactory $appFactory
     */
    public function initialize(IAppFactory $appFactory);
    /**
     * This returns an IExecutable object that can be executed.
     * @return IExecutable
     */
    public function getExecutable(IAppFactory $appFactory);
}
