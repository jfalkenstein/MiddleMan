<?php

namespace PB\Core\Enumerations;

/**
 * Provides a base enumeration functionality (otherwise missing from PHP). This is
 * an abstract class, meant to be extended for full functionality.
 * 
 * The bulk of this code was found online via a code sample. You can view that sample
 * [here](http://stackoverflow.com/questions/254514/php-and-enumerations).
 *
 * @author jfalkenstein
 */
abstract class Enum {
    private static $constCacheArray = NULL; /**< An associative array, caching the constants
     * and their associated values as they are used.
     */
    
    /**
     * Obtains an array of constants associated with the class that is extending this class.
     * @return array
     */
    private static function getConstants() {
        if (self::$constCacheArray == NULL) {
            self::$constCacheArray = [];
        }
        //Get the name to the current class calling this function (because this is an abstract class)
        $calledClass = get_called_class();
        //If this class's constants don't exist in $constCacheArray, 
        //cache them with the key of this class's name.
        if (!array_key_exists($calledClass, self::$constCacheArray)) {
            $reflect = new \ReflectionClass($calledClass);
            self::$constCacheArray[$calledClass] = $reflect->getConstants();
        }
        return self::$constCacheArray[$calledClass];
    }
    
    /**
     * Determines whether the name is a valid name for the present enumeration.
     * @param string $name The name to test for the enumeration.
     * @param boolean $strict Whether or case sensitivity matters.
     * @return boolean
     */
    public static function isValidName($name, $strict = false) {
        $constants = self::getConstants();

        if ($strict) {
            return array_key_exists($name, $constants);
        }

        $keys = array_map('strtolower', array_keys($constants));
        return in_array(strtolower($name), $keys);
    }
    
    /**
     * Checks if the present enumeration has the passed in value.
     * @param int $value The value to check.
     * @param boolean $strict Whether to employ type-checking.
     * @return boolean
     */
    public static function isValidValue($value, $strict = true) {
        $values = array_values(self::getConstants());
        return in_array($value, $values, $strict);
    }
    
    /**
     * Will return the name of the enumeraton associated with this value.
     * If the value is either a valid name or valid value, it will return the
     * name.
     * 
     * If the value is an invalid name and an invalid value, it will return false.
     * @param int|string $value The value or name of the constant to obtain
     * @return string|boolean
     *  *   If the name is found, it will return the name.
     *  *   If the name is <em>not</em> found, it will return false.
     */
    public static function getName($value){
        $consts = self::getConstants();
        if(is_string($value) && array_key_exists($value, $consts)){
            return $value;
        }
        $lowerVal = is_string($value) ? strtolower($string) : $value;
        foreach($consts as $c => $v){
            if($value === $v || strtolower($c) === $lowerVal){
                return $c;
            }
        }
        return false;
    }
    
    
}
