<?php

namespace PB\Core\Events;

use PB\Core\Exceptions\PbException;
use PB\Core\Interfaces\IModule;
use PB\Core\Request\Request;
use PB\Core\Response\Response;

/**
 * This is a fundamental unit of the MiddleMan application. Passed around by the 
 * EventManager, this class acquires the various data points as it moves through
 * the lifecycle of the application.
 * 
 * The various methods on this class privide simple getters/setters on for these
 * data points.
 * 
 * The given 
 * @author jfalkenstein
 */
class Event {
    private $request;
    private $token; 
    private $module;
    private $data;
    
    private $exception;
    private $response;
    private $serializedResponse;
    
    /**
     * @return Request::Request
     */
    public function getRequest(){
        return $this->request;
    }
    
    public function setRequest(Request $value){
        $this->request = $value;
    }
    
    /**
     * @return Response::Response
     */
    public function getResponse(){
        return $this->response;
    }
    
    public function setResponse(Response $value){
        $this->response = $value;
    }
    
    /**
     * @return Exception::PbException
     */
    public function getException(){
        return $this->exception;
    }
    
    public function setException(PbException $value){
        $this->exception = $value;
    }
    
    /**
     * @return string
     */
    public function getToken(){
        return $this->token;
    }
    
    /**
     * @param string $value
     */
    public function setToken($value){
        $this->token = $value;
    }
    
    /**
     * @return Interfaces::IModule
     */
    public function getModule(){
        return $this->module;
    }
    
    public function setModule(IModule $module){
        $this->module = $module;
    }
    
    /**
     * 
     * @return object
     */
    public function getData(){
        return $this->data;
    }
    
    /**
     * @param object $data
     */
    public function setData($data){
        $this->data = $data;
    }
    
    /**
     * @param string $string
     */
    public function setSerializedResponse($string){
        $this->serializedResponse = $string;
    }
    
    /**
     * @return string
     */
    public function getSerializedResponse(){
        return $this->serializedResponse;
    }
}


