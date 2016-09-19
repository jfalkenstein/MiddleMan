<?php

namespace PB\Core\Interfaces;

use PB\Core\Request\Request;

/**
 * Provides centralized access for module setting, loading, and execution, as well
 * as obtaining the results from said execution.
 * @author jfalkenstein
 */
interface IModuleManager {
    /**
     * Obtains an IModule object.
     * @param string $domain The domain of the module.
     * @param string $moduleName The name of the module.
     * @return IModule
     */
    public function getModule($domain, $moduleName);
    
    /**
     * Obtains configuration, registers dependencies, and initializes the module.
     * @param IAppFactory $appFactory
     * @param IModule $module
     * @see IModule
     */
    public function loadModule(IAppFactory $appFactory, IModule $module);
    
    /**
     * Obtains from the module the IExecutable and then executes it, returning the results.
     * @param IModule $module
     * @param Request $request
     * @param IAppFactory $appFactory
     * @return object The results of the module execution.
     */
    public function executeModule(IModule $module, Request $request, IAppFactory $appFactory);
}
