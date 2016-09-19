<?php

namespace PB\Core\Interfaces;

use PB\Core\Events\Event;

/**
 *
 * @author jfalkenstein
 */
interface IPublisher {
    /**
     * Notifies the IEventManager with an event.
     * @param string $eventName
     * @param Event $event
     */
    public function notify($eventName, Event $event);
}
