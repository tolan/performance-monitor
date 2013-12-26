<?php

/**
 * This script defines class for event manager. It provides registration of events and listeners. When is called method flush then all events are send to
 * target listeners.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Event_Manager implements Performance_Main_Event_Interface_Manager {

    const EVENT_BROADCAST = 'broadcast';
    const EVENT_EMIT      = 'emit';
    const LISTENER_ON     = 'on';
    const LISTENER_ONCE   = 'once';

    /**
     * Stack for events.
     *
     * @var Performance_Main_Event_Action_Abstract[]
     */
    private $_events = array();

    /**
     * Stack for listeners.
     *
     * @var Performance_Main_Event_Listener_Abstract[]
     */
    private $_listeners = array();

    /**
     * Gets all registerd events.
     *
     * @return Performance_Main_Event_Action_Abstract[]
     */
    public function getEvents() {
        return $this->_events;
    }

    /**
     * Gets all registerd listeners.
     *
     * @return Performance_Main_Event_Listener_Abstract[]
     */
    public function getListeners() {
        return $this->_listeners;
    }

    /**
     * This method registers event for broadcast behaviour. It means that this event is send to all listeners over all modules.
     *
     * @param string                                   $eventName Event identificator
     * @param Performance_Main_Event_Interface_Message $message   Message of event
     *
     * @return Performance_Main_Event_Manager
     */
    public function broadcast($eventName, Performance_Main_Event_Interface_Message $message = null) {
        $this->_events[] = $this->_createEvent($eventName, $message, self::EVENT_BROADCAST);

        return $this;
    }

    /**
     * This method registers event for emit behaviour. It means that this event is send to all listeners in our module.
     *
     * @param string                                   $eventName Event identificator
     * @param Performance_Main_Event_Interface_Message $message   Message of event
     *
     * @return Performance_Main_Event_Manager
     */
    public function emit($eventName, Performance_Main_Event_Interface_Message $message = null) {
        $this->_events[] = $this->_createEvent($eventName, $message, self::EVENT_EMIT);

        return $this;
    }

    /**
     * This method registers listener for all registered event with right attributes.
     *
     * @param string  $eventName Event identificator
     * @param Closure $closure   Closure instance with function which is called when event is fired
     *
     * @return Performance_Main_Event_Manager
     */
    public function on($eventName, Closure $closure) {
        $this->_listeners[] = $this->_createListener($eventName, $closure, self::LISTENER_ON);

        return $this;
    }

    /**
     * This method registers listener for first registered event with right attributes. It means that it is called only one.
     *
     * @param string  $eventName Event identificator
     * @param Closure $closure   Closure instance with function which is called when event is fired
     *
     * @return Performance_Main_Event_Manager
     */
    public function once($eventName, Closure $callback) {
        $this->_listeners[] = $this->_createListener($eventName, $callback, self::LISTENER_ONCE);

        return $this;
    }

    /**
     * This method flush all events to listeners. It means that it takes all evens and resolve their attributes and send it to right listener.
     *
     * @return Performance_Main_Event_Manager
     */
    public function flush() {
        while($this->getEvents()) {
            foreach ($this->getEvents() as $key => $event) {
                /* @var $event Performance_Main_Event_Action_Abstract */
                if ($event instanceof Performance_Main_Event_Action_Emit) {
                    $this->_flushEmit($event);
                } elseif ($event instanceof Performance_Main_Event_Action_Broadcast) {
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
     * @return Performance_Main_Event_Manager
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
     * @param string                                   $eventName Event identificator
     * @param Performance_Main_Event_Interface_Message $message   Message of event
     * @param string                                   $type      Type of event (broadcast|emit)
     *
     * @return Performance_Main_Event_Action_Abstract
     */
    private function _createEvent($eventName, Performance_Main_Event_Interface_Message $message = null, $type = self::EVENT_BROADCAST) {
        switch ($type) {
            case self::EVENT_BROADCAST:
                $event = new Performance_Main_Event_Action_Broadcast();
                break;
            case self::EVENT_EMIT:
                $event  = new Performance_Main_Event_Action_Emit();
                $source = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
                $event->setModule($this->_getModule($source[2]['class']));
                break;
            default :
                throw new Performance_Main_Event_Exception('Undefined event type.');
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
        $result = ltrim(strstr($classname, '_'), '_');

        return strstr($result, '_', true);
    }

    /**
     * It creates listener by given event name and closure function.
     *
     * @param string  $eventName Event identificator
     * @param Closure $closure   Closure instance with function which is called when event is fired
     * @param string  $type      Type of listener (on|once)
     *
     * @return Performance_Main_Event_Listener_Abstract
     *
     * @throws Performance_Main_Event_Exception Throws when listener is undefined
     */
    private function _createListener($eventName, Closure $closure, $type = self::LISTENER_ON) {
        switch ($type) {
            case self::LISTENER_ON:
                $listener = new Performance_Main_Event_Listener_On();
                break;
            case self::LISTENER_ONCE:
                $listener = new Performance_Main_Event_Listener_Once();
                break;
            default :
                throw new Performance_Main_Event_Exception('Undefined listener type.');
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
     * @param Performance_Main_Event_Action_Emit $event Event instance
     *
     * @return Performance_Main_Event_Manager
     *
     * @throws Performance_Main_Event_Exception Throws when event is undefined
     */
    private function _flushEmit(Performance_Main_Event_Action_Emit $event) {
        foreach ($this->getListeners() as $key => $listener) {
            if (
                $listener instanceof Performance_Main_Event_Listener_Once
                && $this->_matchEventName($event, $listener) && $event->getModule() === $listener->getModule()
            ) {
                $this->_flushEvent($event, $listener);
                unset($this->_listeners[$key]);
            } elseif (
                $listener instanceof Performance_Main_Event_Listener_On &&
                $this->_matchEventName($event, $listener) && $event->getModule() === $listener->getModule()
            ) {
                $this->_flushEvent($event, $listener);
            } else {
                throw new Performance_Main_Event_Exception('Undefined event type.');
            }
        }

        return $this;
    }

    /**
     * It flush broadcast event to all listeners.
     *
     * @param Performance_Main_Event_Action_Broadcast $event Event instance
     *
     * @return Performance_Main_Event_Manager
     */
    private function _flushBroadcast(Performance_Main_Event_Action_Broadcast $event) {
        foreach ($this->getListeners() as $key => $listener) {
            if ($listener instanceof Performance_Main_Event_Listener_Once && $this->_matchEventName($event, $listener)) {
                $this->_flushEvent($event, $listener);
                unset($this->_listeners[$key]);
            } elseif ($listener instanceof Performance_Main_Event_Listener_On && $this->_matchEventName($event, $listener)) {
                $this->_flushEvent($event, $listener);
            }
        }

        return $this;
    }

    /**
     * This match names of event and listener.
     *
     * @param Performance_Main_Event_Action_Abstract   $event    Event instance
     * @param Performance_Main_Event_Listener_Abstract $listener Listener instance
     *
     * @return boolean
     */
    private function _matchEventName(Performance_Main_Event_Action_Abstract $event, Performance_Main_Event_Listener_Abstract $listener) {
        $eventName       = $event->getName();
        $listenerPattern = $listener->getName();

        // TODO [kovar] - implement pattern resolving

        return $eventName == $listenerPattern || $listenerPattern == 'all';
    }

    /**
     * It flush event to concrete listener.
     *
     * @param Performance_Main_Event_Action_Abstract   $event    Event instance
     * @param Performance_Main_Event_Listener_Abstract $listener Listener instance
     *
     * @return Performance_Main_Event_Manager
     */
    private function _flushEvent(Performance_Main_Event_Action_Abstract $event, Performance_Main_Event_Listener_Abstract $listener) {
        $callback   = $listener->getClosure();
        $reflClass  = new ReflectionFunction($callback);
        $parameters = $reflClass->getParameters();

        if (count($parameters) > 0) {
            $callback($event->getData());
        } else {
            $callback();
        }

        return $this;
    }
}
