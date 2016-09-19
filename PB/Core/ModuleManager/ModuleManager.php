<?php

namespace PB\Core\ModuleManager;

use PB\Core\Exceptions\ModuleExecutionException;
use PB\Core\Exceptions\ModuleLoadException;
use PB\Core\Exceptions\PbException;
use PB\Core\Interfaces\IAppFactory;
use PB\Core\Interfaces\IModule;
use PB\Core\Interfaces\IModuleManager;
use PB\Core\Request\Request;

/**
 * The centralized service used for obtaining, initializing, and executing modules.
 *
 * For more information regarding the creation of modules, see @ref md_documentation_modules.
 * @author jfalkenstein
 */
class ModuleManager implements IModuleManager {
    /**
     * Attempts to locate the module class and then instantiate it with the domain and module names.
     * 
     * @param string $domain
     * @param string $moduleName
     * @return Interfaces::IModule
     * @throws Exceptions::ModuleLoadException if the ModuleManager cannot find the requested module.
     */
    public function getModule($domain, $moduleName) {
        $className = 'PB\Modules\\' . $domain . '\\' . $moduleName . '\\' . 'mod_' . $moduleName;
        if(!class_exists($className)){
           throw new ModuleLoadException("The requested module ($moduleName) does not exist in the domain $domain");
        }
        $module = new $className();
        return $module;
    }
    
    /**
     * Initializes the provided module by merging it's provided configuration array
     * into the Interfaces::IConfigManager's configArray, registering any dependencies
     * in the module, and then running any initializatin code the module might have.
     * @param IAppFactory $appFactory Used to obtain services and then is injected into the module
     * for initialization.
     * @param IModule $module The module being initialized.
     * @throws Exceptions::ModuleLoadException if there was an exception calling any of the
     * initialization functions on the module.
     */
    public function loadModule(IAppFactory $appFactory, IModule $module) {
        try{
            $config = $appFactory->ConfigManager();
            //1. Merge in any module Configs.
            $config->addConfig($module->getConfigs());
            //2. Register module dependencies.
            $module->registerDependencies($appFactory->RegistrationService());
            //3. Initialize.
            $module->initialize($appFactory);
        }catch (PbException $ex){
            throw $ex;
        }catch (\Exception $ex) {
            throw new ModuleLoadException("An exception was encountered while attempting to load {$module->getName()}.",0,$ex);
        }
    }
    
    /**
     * Obtains the Interfaces::IExecutable from the module, then executes it, injecting
     * the Request::Request and Interfaces::IAppFactory to give the module everything
     * it needs to execute the request.
     * @param IModule $module
     * @param Request $request
     * @param IAppFactory $appFactory
     * @return type
     * @throws Exceptions::ModuleExecutionException if an exception was encountered during execution.
     */
    public function executeModule(IModule $module, Request $request, IAppFactory $appFactory){
        try{
            $executable = $module->getExecutable($appFactory);
            return $executable->execute($request, $appFactory);
        } catch (PbException $ex){
            throw $ex;
        } catch (\Exception $ex) {
            throw new ModuleExecutionException("There was a problem executing the {$module->getName()} module.",0,$ex);
        }
    }

}
