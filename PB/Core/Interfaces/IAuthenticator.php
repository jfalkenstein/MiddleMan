<?php

namespace PB\Core\Interfaces;

use PB\Core\Request\Request;

/**
 * Handles the authentication process for a given request.
 * @author jfalkenstein
 */
interface IAuthenticator {
    /**
     * @return bool|string The return token if authentication passes, or false if it fails.
     */
    public function authenticate(Request $request);
}
