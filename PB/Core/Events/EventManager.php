<?php

namespace PB\Core\Events;

use PB\Core\Interfaces\IEventManager;
use PB\Core\Interfaces\ISubscriber;

/**
 * This is the centralized means by which events can be passed throughout the application.
 *  *   If a class needs to publish events, it needs to implement the Interfaces::IPublisher interface.
 *  *   If a class needs to subscribe to events, it needs to implement the Interfaces::ISubscriber interface.
 *  *   Alternatively, if it is desired that only a single function may be called, then listen() can be used
 *      instead of implementing ISubscriber. This would be a lighter-weight version. This method is not actually
 *      used anywhere within MiddleMan's core code, but it is avaiable for modules.
 * @author jfalkenstein
 */
class EventManager implements IEventManager {
    private $listeners = []; /**< An associative array of all those classes/
     * functions subscribed to events,
     */
    
    /**
     * Registers a subscriber for events. It requires the class implement Interfaces::ISubscriber
     * in order for it to subscribe, because it makes use of the @link Interfaces::ISubscriber::getSubscribedEvents()
     * getSubscribedEvents() @endlink method.
     */
    public function addSubscriber(ISubscriber $sub) {
        $listeners = $sub->getSubscribedEvents();
        foreach($listeners as $eventName => $listener){
            $this->listen($eventName, [$sub,$listener]);
        }
    }
    
    /**
     * Publishes an event.
     * @param string $eventName The name of the event to publish.
     * @param Event $event The event object being passed to the subscribers.
     */
    public function dispatch($eventName, $event) {
        foreach($this->listeners[$eventName] as $listener){
            call_user_func($listener, $event);
        }
    }
    
    /**
     * Adds a callable to the subscriptions for a particular event
     * @param string $event The name of the event being subscribed to.
     * @param callable $callback The callable to be called when the event is published.
     */
    public function listen($event, $callback) {
        $this->listeners[$event][] = $callback;
    }

}
