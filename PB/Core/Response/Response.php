<?php

namespace PB\Core\Response;

/**
 * This is the response object that holds all data necessary for creating a response
 * to the request. It is produced by the Interfaces::IResponseFactory.
 *
 * @author jfalkenstein
 */
class Response {
    public $responseType; /**< The int constant from the Enumerations::ResponseType enum.*/
    public $message; /**< Any additional message to accompany the response, 
     * such as in the case of an exception or authentication failure.
     */
    public $data;/**< The actual data object representing the executed module's results.
     * In the case of an exception, this will instead be the name of the actual exception
     * encountered.
     */
    public $domain;/**< The name of the domain to which the executed module belongs.*/
    public $module;/**< The name of the module executed. */
    public $serverToken;/**< The returned token to ensure the server's response 
     * can authenticate with the client. */
    public $returnVals; /**< An array of key/value pairs that were sent with the request
     * that are then returned with the response to ensure proper handling on the client.
     */
}
