<?php

namespace PB\Core\Interfaces;

use PB\Core\Events\Event;

/**
 * This is the centralized means by which events can be passed throughout the application.
 * @author jfalkenstein
 */
interface IEventManager {
    /**
     * Adds a callable to the subscriptions for a particular event
     * @param string $event The name of the event being subscribed to.
     * @param callable $callback The callable to be called when the event is published.
     */
    public function listen($event, $callback);
    /**
     * Publishes an event.
     * @param string $eventName The name of the event to publish.
     * @param Events::Event $event The event object being passed to the subscribers.
     */
    public function dispatch($eventName, $event);
    /**
     * Registers a subscriber for events.
     * @param ISubscriber $sub The subscribing object.
     */
    public function addSubscriber(ISubscriber $sub);
}
