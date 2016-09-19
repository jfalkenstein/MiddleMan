<?php

namespace PB\Core\Interfaces;

/**
 * The interface a subscribing class must implement in order to subscribe to events.
 * @author jfalkenstein
 */
interface ISubscriber {
    /**
     * @return array The array of event names and listener function names.
     */
    public function getSubscribedEvents();
    
}
