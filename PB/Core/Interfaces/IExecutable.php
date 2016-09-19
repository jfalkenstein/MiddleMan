<?php

namespace PB\Core\Interfaces;

use PB\Core\Request\Request;
use stdClass;

/**
 * This is an essential class for the module system. An executable class <em>must</em>
 * implement this, which allows MiddleMan to know how to execute its functionality.
 * @see IModule
 * @author jfalkenstein
 */
interface IExecutable {
    /**
     * Executes the module's function.
     * @param Request::Request $request The request that requeted this module.
     * @param IAppFactory $appFactory Injects the IAppFactory to provide access to services.
     * @return object Whatever kind of data the module desires to return. It should be an object
     * or associative array. Whatever it is, it must be json-encodable so that it can be
     * serialized into the response.
     */
    public function execute(Request $request, IAppFactory $appFactory);
}
