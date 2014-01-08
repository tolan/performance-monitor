<?php

namespace PF\Main\Event\Interfaces;

/**
 * This script defines interface for event manager.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Manager {

    /**
     * This method registers event for emit behaviour. It means that this event is send to all listeners in our module.
     *
     * @param string                            $eventName Event identificator
     * @param \PF\Main\Event\Interfaces\Message $message   Message of event
     */
    public function emit($eventName, Message $message = null);

    /**
     * This method registers event for broadcast behaviour. It means that this event is send to all listeners over all modules.
     *
     * @param string                            $eventName Event identificator
     * @param \PF\Main\Event\Interfaces\Message $message   Message of event
     */
    public function broadcast($eventName, Message $message = null);

    /**
     * This method registers listener for all registered event with right attributes.
     *
     * @param string   $eventName Event identificator
     * @param \Closure $closure   Closure instance with function which is called when event is fired
     */
    public function on($eventName, \Closure $callback);

    /**
     * This method registers listener for first registered event with right attributes. It means that it is called only one.
     *
     * @param string   $eventName Event identificator
     * @param \Closure $closure   Closure instance with function which is called when event is fired
     */
    public function once($eventName, \Closure $callback);

    /**
     * This method flush all events to listeners. It means that it takes all evens and resolve their attributes and send it to right listener.
     */
    public function flush();

    /**
     * It make deregistration all events and listeners.
     */
    public function clean();
}
