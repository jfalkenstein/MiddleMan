<?php

namespace PB\Core\Interfaces;

/**
 * Provides the central accesspoint to application and module configuration.
 * @author jfalkenstein
 */
interface IConfigManager {
    /**
     * Obtains the passphrase for authentication.
     * @return string
     */
    public function getPassphrase();
    /**
     * Obtains the salt for authentication.
     * @return string 
     */
    public function getSalt();
    /**
     * Obtains the return salt for authentication
     * @return string 
     */
    public function getReturnSalt();
    /**
     * Obtains a value from the application configuration.
     * @param string|array $key If this is an array, each consecutive element 
     * in the array will provide a further key.
     * @param array $params In the event that the config value is a function, these
     *          params will be passed in as the parameters for that function.
     * @return string|boolean|int
     */
    public function getValue($key, $params = null);
    /**
     * This will merge in additional config arrays into the master config array.
     * This is mostly used to add module configs.
     * @param array $configArray
     */
    public function addConfig(Array $configArray);
}
