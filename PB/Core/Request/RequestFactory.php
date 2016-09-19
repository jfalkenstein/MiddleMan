<?php

namespace PB\Core\Request;

use PB\Core\Exceptions\RequestException;
use PB\Core\Interfaces\IConfigManager;
use PB\Core\Interfaces\IRequestFactory;

/**
 * Creates a Request object from the superglobals. 
 * @author jfalkenstein
 */
class RequestFactory implements IRequestFactory{
    private $config; /**< The Interfaces::IConfigManager used by this class.*/
    public function __construct(IConfigManager $config) {
        $this->config = $config;
    }
    /**
     * Obtains the filtered request info from the superglobals and returns
     * a Request object.
     * @return Request
     * @throws Exceptions::RequestException if there are no GET input values (there should be at LEAST one, directed
     * by the .htaccess file that redirects all url segments to index.php).
     */
    public function packageRequest() {
        $rawGets = filter_input_array(INPUT_GET,FILTER_SANITIZE_STRING);
        $rawPosts = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $reqMethod = filter_input(INPUT_SERVER,"REQUEST_METHOD",FILTER_SANITIZE_STRING);
        
        
        $customHeaders = $this->getCustomHeaders();              
        $request = new Request();
        $request->RequestMethod = $reqMethod;
        
        $this->addData($request, $rawGets, $rawPosts, $customHeaders);
        $this->specifyResponseType($reqMethod, $request);
        return $request;
    }
    
    private function specifyResponseType($reqMethod, Request $request){
        if(isset($request->UrlParams[0])){
            $requestType = strtolower($request->UrlParams[0]);
            $this->config->addConfig(['serializer'=>['requested'=> $requestType]]);
            return;
        }
        if($reqMethod === "GET"){//If request is GET and no request type is specified...
            //Assume jsonp is desired response and set desired response
            $this->config->addConfig(['serializer'=>['requested'=>'jsonp']]);
            return;
        }
        if($reqMethod === "POST"){//If no serialization method specified, assume json
            $this->config->addConfig(['serializer'=>['requested'=>'json']]);
        }
    }
    
    private function getCustomHeaders(){
        $rawHeaders = filter_input_array(INPUT_SERVER, FILTER_SANITIZE_STRING);
        $customPrefix = $this->config->getValue(['requestFactory','customHeaderPrefix']);
        $customPrefix = str_replace(["-", " "],"_", $customPrefix);
        $customPrefix = 'HTTP_' . strtoupper($customPrefix);
        $customHeaders = [];
        
        foreach($rawHeaders as $k => $v){
            if(strpos($k,$customPrefix) !== false){
                $key = str_replace($customPrefix,"",$k);
                $customHeaders[$key] = $v;
            }
        }
        return $customHeaders;
    }
    
    
    /**
     * This parses out the GET values and addes them to the Request
     * object
     * @param array $gets The filtered array of GET keys & values.
     * @param Request $request The request object being assembled.
     * @throws Exceptions::RequestException if...
     * *    There is not a "path" GET value. This should be added by Apache as
     * configured by the .htaccess file, redirecting all urls to index.php.
     * *    If there are not AT LEAST two url segments past the initial ip/port
     * segments. This is because segment 1 must indicate domain and segment 2 must indicate
     * module. Without these, MiddleMan cannot operate.
     */
    private function parseGets(Array $gets, Request $request){
        //Check for the path value (added by Apache).
        if(!isset($gets['path'])){
            throw new RequestException("Path must be specified.");
        }
        $path = $gets['path'];
        //Separate the path into an array of segments.
        $segments = explode('/', $path);
        if(count($segments)<2 || $segments[1] === ""){ //If there are <2 segments...
            throw new RequestException("Domain and Module must be specified in the url.");
        }
        $request->Domain = $segments[0]; //Domain = first segment
        $request->Module = $segments[1]; //Module = Second segment.
        
        //All further segments should be added as unnamed & positional parameter values.
        $params = [];
        for($i = 2;$i<count($segments); $i++){
            $params[] = $segments[$i];
        }
        $request->UrlParams = $params; //Assign the params to the request object.
        
        //Loop through the get values...
        foreach($gets as $key => $val){
            /*
             * Php will receive a certain format of query string key/values and
             * automatically convert them to an array. Thus, if the query string read this:
             * 
             *     myArray[]=blue&myArray[]=red
             * 
             * This would automatically be converted to:
             * 
             *     myArray = ['blue','red']
             * 
             * Further, this will also convert comma-separated values to an array.
             * 
             * Thus, if the value isn't already an array, but the value has commas,
             * convert it to an array. You cannot explode an array (php gives an error),
             * which is why this check is necessary.
             */
            if(!is_array($val) && strpos($val,',') !== false){
                $request->Data[$key] = explode(",",$val);
                continue;
            }
            $request->Data[$key] = $val; //Assign the key/value pair to the request's Data property.
        }
    }
    
    /**
     * This parses out the post values and adds them to the Request object.
     * @param array $posts
     * @param Request $request
     */
    private function parsePosts(Array $posts, Request $request){       
        foreach($posts as $key => $val){
            $request->Data[$key] = $val;
        }
    }
    
    /**
     * Adds any return values to the request specifically as such to ensure they
     * are included in the response.
     * 
     * 1.   This will search the configManager for the key associated with returnValues.
     * 2.   It will then search the request's Data property for a value with that key.
     * 3.   If found, this will be an array of keys associated with other data points
     * included with the request. Thus, this will search the Data property for each
     * key in the ReturnValues and assign them to the ReturnVals array on the request.
     * 
     * @param \PB\Core\Request\Request $request
     */
    private function addReturnVals(Request $request) {
        $key = $this->config->getValue(['requestFactory','returnValueKey']);
        
        if(is_array($request->Data[$key])){
            foreach($request->Data[$key] as $k){
                $request->ReturnVals[$k] = $request->Data[$k];
            }
            return;
        }        
    }

    private function addData(Request $request, $rawGets, $rawPosts, $customHeaders) {
        if(is_null($rawGets)){
            throw new RequestException("Improper URL - NO path or get parameters given.");
        }
        $this->parseGets($rawGets, $request);
        
        if($request->RequestMethod === "POST" && is_null($rawPosts)){
            $post_body = file_get_contents('php://input');
            $rawPosts = [];
            parse_str($post_body, $rawPosts);
        }
        if(!is_null($rawPosts)){
            $this->parsePosts($rawPosts, $request);
        }
        
        if(count($customHeaders) > 0){
            $this->parsePosts($customHeaders, $request);
        }
        
        $this->addReturnVals($request);
    }

}
