<?php

namespace PB\Core\Response;

use PB\Core\Interfaces\IConfigManager;
use PB\Core\Interfaces\IResponseSerializer;
use PB\Core\Request\Request;

/**
 * Description of HeadersSerializer
 *
 * @author jfalkenstein
 */
class HeadersSerializer implements IResponseSerializer{
    
    private $config;
    private $customPrefix;
    public function __construct(IConfigManager $config) {
        $this->config = $config;
    }
    
    public function Serialize(Request $request, Response $response) {
        $this->customPrefix = $this->config->getValue(['requestFactory','customHeaderPrefix']);
        $this->setHeaders($response);
    }
    
    private function setHeaders($response, $prefix=""){
        foreach($response as $prop => $val){
            if(is_object($val) || is_array($val)){
                $newPrefix = (strlen($prefix) === 0) ? "$prop-" : "-$prop-";
                $this->setHeaders($val, $newPrefix);
                return;
            }
            if(is_null($val)){
                continue;
            }
            $header = $this->createHeader("{$prefix}{$prop}", $val);
            header($header);
        }
    }
    
    private function createHeader($key, $value){
        $headerName = "{$this->customPrefix}{$key}";
        $header = "{$headerName}: $value";
        return $header;
    }
}
