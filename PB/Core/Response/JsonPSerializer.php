<?php

namespace PB\Core\Response;

use PB\Core\Interfaces\IConfigManager;
use PB\Core\Interfaces\IResponseSerializer;
use PB\Core\Request\Request;

/**
 * This is the default implementation of Interfaces::IResponseSerializer.
 * It will receive the Request::Request object and the Response object and
 * return them in JSONP format.
 * 
 * For more information about JSONP and the output, see @ref jsonp.
 *
 * @author jfalkenstein
 */
class JsonPSerializer implements IResponseSerializer{
    private $config; /**< The Interfaces::IConfigManager used by this class. */
    
    public function __construct(IConfigManager $config) {
        $this->config = $config;
    }
    
    /**
     * Receives the Request and Response objects and encodes them into a JSONP string.
     * @param Request::Request $request
     * @param Response $response
     * @return string
     */
    public function Serialize(Request $request, Response $response) {
        header('Content-Type: application/javascript');
        //Obtains the configurations for the jsonp serializer from the ConfigManager.
        $config = $this->config->getValue(['serializer','jsonp']);
        //Get the data object from the request.
        $reqData = isset($request->Data) ? $request->Data : null;
        //Get the name of the key to look for re: the callback function
        $callbackKey = $config['callbackKey'];
        //Get the default callback name, in case the request doesn't specify it.
        $default = $config['defaultCallbackName'];
        //Obtain from the request data the callback name, if it can be found, otherwise, use the default.
        $callbackName = !is_null($reqData) ? $reqData[$callbackKey] : $default;
        //Whether to make the json response human-readable.
        $prettyPrint = $config['prettyPrint'];
        //Get the key to look for the onError callback name.
        $errorKey = $config['errorKey'];
        //Get the onError callback name from the request data.
        $errorCb = !is_null($reqData) ? $reqData[$errorKey] : null;
        //Get the key to look for the finally callback name.
        $finallyKey = $config['finallyKey'];
        //Get the finally callback name.
        $finallyCb = !is_null($reqData) ? $reqData[$finallyKey] : null;
        //json encode the response object to a string.
        $data = json_encode($response,($prettyPrint ? JSON_PRETTY_PRINT : 0));
        //json encode the return values for the finally call
        $returnVals = json_encode($response->returnVals,($prettyPrint ? JSON_PRETTY_PRINT : 0));
        //Format the string into jsonp and return the string.
        return $this->format($callbackName, $errorCb, $finallyCb, $data, $returnVals);
    }
    
    /**
     * This receives the various pieces of information necessary to ouput
     * a jsonP string within a try/catch/finally block and then creates the
     * jsonp response string.
     * @param string $callbackName
     * @param string $errorCb
     * @param string $finallyCb
     * @param string $data
     * @return string
     */
    private function format ($callbackName, $errorCb, $finallyCb, $data, $returnVals){
        return 
        "try{\n"
            . "$callbackName({$data});\n"
        . "}catch(e){\n"
            . "$errorCb(e);\n"
        . "}finally{\n"
            . "$finallyCb($returnVals);\n"
        . "}";
    }
}
