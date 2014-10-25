<?php

namespace PM\Main\Event\Listener;

use PM\Main\Event\Exception;
use PM\Main\Event\Interfaces\Listener;

/**
 * This script defines abstract class for event listener.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractListener implements Listener {

    /**
     * Name of event
     *
     * @var string
     */
    private $_name = null;

    /**
     * Closure function
     *
     * @var \Closure
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
     * @throws \PM\Main\Event\Exception Throws when name is not set.
     */
    public function getName() {
        if ($this->_name === null) {
            throw new Exception('Event name for listener is not set.');
        }

        return $this->_name;
    }

    /**
     * Sets name of event.
     *
     * @param string $name Name of event
     *
     * @return \PM\Main\Event\Listener\AbstractListener
     */
    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    /**
     * Get closure function.
     *
     * @return \Closure
     *
     * @throws \PM\Main\Event\Exception Throws when closure is not set
     */
    public function getClosure() {
        if ($this->_closure === null) {
            throw new Exception('Listener has not set a callback.');
        }

        return $this->_closure;
    }

    /**
     * Sets closure function.
     *
     * @param \Closure $closure Closure function
     *
     * @return \PM\Main\Event\Listener\AbstractListener
     */
    public function setClosure(\Closure $closure) {
        $this->_closure = $closure;

        return $this;
    }

    /**
     * Gets name of module where listener was created.
     *
     * @return string
     */
    public function getModule() {
        return $this->_module;
    }

    /**
     * Sets name of module where listener was created.
     *
     * @param string $module Name of module
     *
     * @return \PM\Main\Event\Listener\AbstractListener
     */
    public function setModule($module) {
        $this->_module = $module;

        return $this;
    }
}
