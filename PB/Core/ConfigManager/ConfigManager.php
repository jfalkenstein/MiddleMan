<?php

namespace PB\Core\ConfigManager;

use PB\Core\Interfaces\IConfigManager;
use const DS;
use const ROOT_DIR;

/**
 * Provides the central accesspoint to application and module configuration.
 * Initially, the ConfigManager will load up config/config.php. This file is a 
 * simple php associative array with key/value pairs and a nested structure.
 * @author jfalkenstein
 */
class ConfigManager implements IConfigManager {
    private $configArray; /**< The configuration array that is compiled first from
     * config/config.php and then from other configuration arrays within modules.
     * It is an associative array with a nested structure.
     */

    /**
     * Upon construction, ConfigManager will populate $configArray with the contents
     * of config/config.php.
     */
    public function __construct() {
        $this->configArray = include ROOT_DIR . DS . "config" . DS . "config.php";
    }

    /**
     * Obtains a value from the application configuration.
     * @param string|array $key If this is an array, each consecutive element 
     * in the array will provide a further key. For example:
     *     @code['serializer','jsonp','callbackKey'] @endcode
     * is equivalent to querying
     *     @code $this->configArray['serializer']['jsonp']['callbackKey'] @endcode
     * @param array $params In the event that the config value is a function, these
     *          params will be passed in as the parameters for that function.
     * @return string|boolean|int
     */
    public function getValue($key, $params = []) {
        $value = $this->configArray;
        if(is_array($key)){
            foreach($key as $subKey){
                $value = $value[$subKey];
            }
        }else{
            $value = $this->configArray[$key];
        }
        if(is_callable($value)){
            $value = $this->getCallableValue($value, $params);
        }
        return $value;        
    }
    /**
     * Obtains a value with the passed in parameters from the callable.
     * @param callable $callable
     * @param array $params
     * @return string|int|boolean
     */
    private function getCallableValue(callable $callable, $params){
        return call_user_func_array($callable, [$params]);
    }
    /**
     * An alias to querying @code 'authenticator'=>'passphrase'. @endcode
     * @return string
     */
    public function getPassphrase() {
        return $this->getValue(['authenticator','passphrase']);
    }
    /**
     * An alias to querying @code 'authenticator'=>'salter'. @endcode
     * @return string
     */
    public function getSalt() {
        return $this->getValue(['authenticator','salter']);
    }
    /**
     * An alias to querying @code 'authenticator'=>'returnSalt' @endcode
     * @return string
     */
    public function getReturnSalt() {
        return $this->getValue(['authenticator','returnSalt']);
    }
    /**
     * This will merge in additional config arrays into the master config array.
     * This is mostly used to add module configs.
     * @param array $configArray
     */
    public function addConfig(array $configArray) {
        $this->configArray = array_merge_recursive($this->configArray, $configArray);
    }
}
