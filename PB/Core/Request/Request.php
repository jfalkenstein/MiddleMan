<?php

namespace PB\Core\Request;

use ArrayObject;
/**
 * Description of Request
 * @property string $RequestMethod
 * @property string $Domain 
 * @property string $Module
 * @property Array $UrlParams 
 * @property ArrayObject $Data 
 * @property string $authenticationString
 * @property Array $ReturnVals
 * @author jfalkenstein
 */
class Request {
    public $RequestMethod;/**< The Http method used for this request (i.e. GET, POST, etc...)*/
    public $Domain; /**< The module domain for this request. */
    public $Module; /**<The module to be executed for this request. */
    public $UrlParams;/**<The remaining url segments for this request.*/
    public $ReturnVals;/**< The keys of values to return with the response. */
    public $Data; /**<The Get & Post data from this request.*/
    
    public function __construct() {
        $this->Data = new ArrayObject();
    }
    
    /**
     * Magic Method. Any properties unknown to this class will be returned from
     * the Data object, if they exist.
     * @param string $name
     */
    public function __get($name) {
        if(isset($this->Data[$name])){
            return $this->Data[$name];
        }
    }
    /**
     * Magic Method. Any properies being set unknown to this class will be set on the
     * Data object instead.
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value) {
        $this->Data[$name] = $value;
    }
}
