<?php

namespace PB\Core\Interfaces;

/**
 * This is the main controlling class of the middleman application.
 * It runs the whole lifecycle from request to response.
 * 
 * @author jfalkenstein
 */
interface IApplication {
    /**
     * This initiates the application lifecycle. 
     */
    public function run();
}
