<?php

namespace PB\Core\Response;

use PB\Core\Interfaces\IResponseSerializer;
use PB\Core\Request\Request;

/**
 * Description of JsonSerializer
 *
 * @author jfalkenstein
 */
class JsonSerializer implements IResponseSerializer{
    private $config; /**< The Interfaces::IConfigManager used by this class. */
    public function __construct(\PB\Core\Interfaces\IConfigManager $config) {
        $this->config = $config;
    }
    /**
     * Receives the Request and Response objects and encodes them into a json string.
     * @param Request::Request $request
     * @param Response $response
     * @return string
     */
    public function Serialize(Request $request, Response $response) {
        header('Content-Type: application/json');
        //Obtains the configurations for the jsonp serializer from the ConfigManager.
        $config = $this->config->getValue(['serializer','json']);
        //Whether to make the json response human-readable.
        $prettyPrint = $config['prettyPrint'];
        //json encode the response object to a string.
        $data = json_encode($response,($prettyPrint ? JSON_PRETTY_PRINT : 0));
        return $data;
    }

}
