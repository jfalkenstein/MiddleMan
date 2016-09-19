<?php

namespace PB\Core\ModuleManager;

use PB\Core\Interfaces\IModule;

/**
 * This is an abstract class that must be extended by all %Modules. It provides
 * some basic implementation for several functions (though such implementations
 * could be overridden.).
 *  
 * For more information regarding the creation of modules, see @ref md_documentation_modules.
 * 
 * @author jfalkenstein
 */
abstract class Module implements IModule{
    
    /**
     * Obtains the domain of the module through reflecting on its namespace.
     * @return string
     */
    public function getDomain() {
        $ref = new \ReflectionClass($this);
        $ns = $ref->getNamespaceName();
        $segments = explode('\\',$ns);
        $domain = $segments[(count($segments)-2)];
        return $domain;
    }
    
    /**
     * Obtains the name of the module by reflecting on its class name and removing
     * the prefixed "mod_".
     * @return string
     */
    public function getName() {
        $ref = new \ReflectionClass($this);
        $name = $ref->getShortName();
        $fixedName = str_replace("mod_", "", $name);
        return $fixedName;
    }
    
    /**
     * Obtains the associative array of configuration values for the module by
     * searching the module's directory for a "config.php" file and (if it exists)
     * loading it and returning it.
     * 
     * If config.php doesn't exist, an empty array will be returned.
     * @return Array
     */
    public function getConfigs() {
        $fileToCheck = 
                ROOT_DIR . DS . 
                'PB' . DS . 
                'Modules' . DS .
                $this->getDomain() . DS .
                $this->getName() . DS .
                'config.php';
        if(!file_exists($fileToCheck)){
            return [];
        }
        try{
            $returnedValue = include $fileToCheck;
            if(!is_array($returnedValue)){
                return [];
            }
            return $returnedValue;
        } catch (Exception $ex) {
            return [];
        }
    }
}
