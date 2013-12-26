<?php

/**
 * This script defines abstract class for event listener.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Event_Listener_Abstract implements Performance_Main_Event_Interface_Listener {

    /**
     * Name of event
     *
     * @var string
     */
    private $_name = null;

    /**
     * Closure function
     *
     * @var Closure
     */
    private $_closure = null;

    /**
     * Module name where listener wass registred.
     *
     * @var string
     */
    private $_module = null;

    /**
     * Gets name of event.
     *
     * @return string
     *
     * @throws Performance_Main_Event_Exception Throws when name is not set.
     */
    public function getName() {
        if ($this->_name === null) {
            throw new Performance_Main_Event_Exception('Event name for listener is not set.');
        }

        return $this->_name;
    }

    /**
     * Sets name of event.
     *
     * @param string $name Name of event
     *
     * @return Performance_Main_Event_Listener_Abstract
     */
    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get closure function.
     *
     * @return Closure
     *
     * @throws Performance_Main_Event_Exception Throws when closure is not set
     */
    public function getClosure() {
        if ($this->_closure === null) {
            throw new Performance_Main_Event_Exception('Listener has not set a callback.');
        }

        return $this->_closure;
    }

    /**
     * Sets closure function.
     *
     * @param Closure $closure Closure function
     *
     * @return Performance_Main_Event_Listener_Abstract
     */
    public function setClosure(Closure $closure) {
        $this->_closure = $closure;

        return $this;
    }

    /**
     * Gets name of module where was listener created.
     *
     * @return string
     */
    public function getModule() {
        return $this->_module;
    }

    /**
     * Sets name of module where was listener created.
     *
     * @param string $module Name of module
     *
     * @return Performance_Main_Event_Listener_Abstract
     */
    public function setModule($module) {
        $this->_module = $module;

        return $this;
    }
}
