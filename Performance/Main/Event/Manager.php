<?php

namespace PF\Main\Event;

/**
 * This script defines class for event manager. It provides registration of events and listeners. When is called method flush then all events are send to
 * target listeners.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Manager implements Interfaces\Manager {

    const EVENT_BROADCAST = 'broadcast';
    const EVENT_EMIT      = 'emit';
    const LISTENER_ON     = 'on';
    const LISTENER_ONCE   = 'once';

    /**
     * Stack for events.
     *
     * @var \PF\Main\Event\Action\AbstractAction[]
     */
    private $_events = array();

    /**
     * Stack for listeners.
     *
     * @var \PF\Main\Event\Listener\AbstractListener[]
     */
    private $_listeners = array();

    /**
     * Gets all registerd events.
     *
     * @return \PF\Main\Event\Action\AbstractAction[]
     */
    public function getEvents() {
        return $this->_events;
    }

    /**
     * Gets all registerd listeners.
     *
     * @return \PF\Main\Event\Listener\AbstractListener[]
     */
    public function getListeners() {
        return $this->_listeners;
    }

    /**
     * This method registers event for broadcast behaviour. It means that this event is send to all listeners over all modules.
     *
     * @param string                            $eventName Event identificator
     * @param \PF\Main\Event\Interfaces\Message $message   Message of event
     *
     * @return \PF\Main\Event\Manager
     */
    public function broadcast($eventName, Interfaces\Message $message = null) {
        $this->_events[] = $this->_createEvent($eventName, $message, self::EVENT_BROADCAST);

        return $this;
    }

    /**
     * This method registers event for emit behaviour. It means that this event is send to all listeners in our module.
     *
     * @param string                            $eventName Event identificator
     * @param \PF\Main\Event\Interfaces\Message $message   Message of event
     *
     * @return \PF\Main\Event\Manager
     */
    public function emit($eventName, Interfaces\Message $message = null) {
        $this->_events[] = $this->_createEvent($eventName, $message, self::EVENT_EMIT);

        return $this;
    }

    /**
     * This method registers listener for all registered event with right attributes.
     *
     * @param string   $eventName Event identificator
     * @param \Closure $closure   Closure instance with function which is called when event is fired
     *
     * @return \PF\Main\Event\Manager
     */
    public function on($eventName, \Closure $closure) {
        $this->_listeners[] = $this->_createListener($eventName, $closure, self::LISTENER_ON);

        return $this;
    }

    /**
     * This method registers listener for first registered event with right attributes. It means that it is called only one.
     *
     * @param string   $eventName Event identificator
     * @param \Closure $closure   Closure instance with function which is called when event is fired
     *
     * @return \PF\Main\Event\Manager
     */
    public function once($eventName, \Closure $callback) {
        $this->_listeners[] = $this->_createListener($eventName, $callback, self::LISTENER_ONCE);

        return $this;
    }

    /**
     * This method flush all events to listeners. It means that it takes all evens and resolve their attributes and send it to right listener.
     *
     * @return \PF\Main\Event\Manager
     */
    public function flush() {
        while($this->getEvents()) {
            foreach ($this->getEvents() as $key => $event) {
                /* @var $event \PF\Main\Event\Action\AbstractAction */
                if ($event instanceof Action\Emit) {
                    $this->_flushEmit($event);
                } elseif ($event instanceof Action\Broadcast) {
                    $this->_flushBroadcast($event);
                }

                unset($this->_events[$key]);
            }
        }

        return $this;
    }

    /**
     * It make deregistration all events and listeners.
     *
     * @return \PF\Main\Event\Manager
     */
    public function clean() {
        foreach (array_keys($this->_events) as $key) {
            unset($this->_events[$key]);
        }

        foreach (array_keys($this->_listeners) as $key) {
            unset($this->_listeners[$key]);
        }

        return $this;
    }

    /**
     * It creates event by given event name and message instance.
     *
     * @param string                            $eventName Event identificator
     * @param \PF\Main\Event\Interfaces\Message $message   Message of event
     * @param string                            $type      Type of event (broadcast|emit)
     *
     * @return \PF\Main\Event\Action\AbstractAction
     *
     * @throws \PF\Main\Event\Exception Throws when you try create undefined event.
     */
    private function _createEvent($eventName, Interfaces\Message $message = null, $type = self::EVENT_BROADCAST) {
        switch ($type) {
            case self::EVENT_BROADCAST:
                $event = new Action\Broadcast();
                break;
            case self::EVENT_EMIT:
                $event  = new Action\Emit();
                $source = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
                $event->setModule($this->_getModule($source[2]['class']));
                break;
            default :
                throw new Exception('Undefined event type.');
        }

        $event->setName($eventName);
        $event->setData($message);

        return $event;
    }

    /**
     * It extracts module name where was listener or event created from class name.
     *
     * @param string $classname Name of class where was listener or event created
     *
     * @return string
     */
    private function _getModule($classname) {
        $result = ltrim(strstr($classname, '\\'), '\\');

        return strstr($result, '\\', true);
    }

    /**
     * It creates listener by given event name and closure function.
     *
     * @param string   $eventName Event identificator
     * @param \Closure $closure   Closure instance with function which is called when event is fired
     * @param string   $type      Type of listener (on|once)
     *
     * @return \PF\Main\Event\Listener\AbstractListener
     *
     * @throws \PF\Main\Event\Exception Throws when listener is undefined
     */
    private function _createListener($eventName, \Closure $closure, $type = self::LISTENER_ON) {
        switch ($type) {
            case self::LISTENER_ON:
                $listener = new Listener\On();
                break;
            case self::LISTENER_ONCE:
                $listener = new Listener\Once();
                break;
            default :
                throw new Exception('Undefined listener type.');
        }

        $listener->setName($eventName);
        $listener->setClosure($closure);

        $source = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
        $listener->setModule($this->_getModule($source[2]['class']));

        return $listener;
    }

    /**
     * It flush emit event to all listeners.
     *
     * @param \PF\Main\Event\Action\Emit $event Event instance
     *
     * @return \PF\Main\Event\Manager
     */
    private function _flushEmit(Action\Emit $event) {
        foreach ($this->getListeners() as $key => $listener) {
            if (
                $listener instanceof Listener\Once
                && $this->_matchEventName($event, $listener) && $event->getModule() === $listener->getModule()
            ) {
                $this->_flushEvent($event, $listener);
                unset($this->_listeners[$key]);
            } elseif (
                $listener instanceof Listener\On &&
                $this->_matchEventName($event, $listener) && $event->getModule() === $listener->getModule()
            ) {
                $this->_flushEvent($event, $listener);
            }
        }

        return $this;
    }

    /**
     * It flush broadcast event to all listeners.
     *
     * @param \PF\Main\Event\Action\Broadcast $event Event instance
     *
     * @return \PF\Main\Event\Manager
     */
    private function _flushBroadcast(Action\Broadcast $event) {
        foreach ($this->getListeners() as $key => $listener) {
            if ($listener instanceof Listener\Once && $this->_matchEventName($event, $listener)) {
                $this->_flushEvent($event, $listener);
                unset($this->_listeners[$key]);
            } elseif ($listener instanceof Listener\On && $this->_matchEventName($event, $listener)) {
                $this->_flushEvent($event, $listener);
            }
        }

        return $this;
    }

    /**
     * This match names of event and listener.
     *
     * @param \PF\Main\Event\Action\AbstractAction     $event    Event instance
     * @param \PF\Main\Event\Listener\AbstractListener $listener Listener instance
     *
     * @return boolean
     */
    private function _matchEventName(Action\AbstractAction $event, Listener\AbstractListener $listener) {
        $eventName       = $event->getName();
        $listenerPattern = $listener->getName();
        $matched         = false;

        if ($eventName == $listenerPattern || $listenerPattern == 'all') {
            $matched = true;
        }

        if ($matched === false) {
            $patterns = explode(' ', $listenerPattern);
            $count    = count($patterns);

            for($i = 0; $i < $count && $matched === false; $i++) {
                $matched = (bool)preg_match('/'.$eventName.'/', trim($patterns[$i]));
            }
        }

        return $matched;
    }

    /**
     * It flush event to concrete listener.
     *
     * @param \PF\Main\Event\Action\AbstractAction     $event    Event instance
     * @param \PF\Main\Event\Listener\AbstractListener $listener Listener instance
     *
     * @return \PF\Main\Event\Manager
     */
    private function _flushEvent(Action\AbstractAction $event, Listener\AbstractListener $listener) {
        $callback   = $listener->getClosure();
        $reflClass  = new \ReflectionFunction($callback);
        $parameters = $reflClass->getParameters();

        if (count($parameters) > 0) {
            $callback($event->getData());
        } else {
            $callback();
        }

        return $this;
    }
}
