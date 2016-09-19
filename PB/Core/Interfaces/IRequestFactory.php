<?php

namespace PB\Core\Interfaces;

/**
 *
 * @author jfalkenstein
 */
interface IRequestFactory {
    
    /**
     * Obtains and packages a request object from the superglobals.
     * @return \PB\Core\Request
     */
    public function packageRequest();
}
