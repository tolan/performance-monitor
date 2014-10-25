<?php

namespace PM\Main\Event\Action;

use PM\Main\Event\Exception;
use PM\Main\Event\Interfaces\Event;

/**
 * This script defines abstract class for event action.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractAction implements Event {

    /**
     * Event name
     *
     * @var string
     */
    private $_name = null;

    /**
     * Data of message.
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * Name of module where was action created
     *
     * @var string
     */
    private $_module = null;

    /**
     * Gets name of event.
     *
     * @return string
     *
     * @throws \PM\Main\Event\Exception Throws when name is not set
     */
    public function getName() {
        if ($this->_name === null) {
            throw new Exception('Event name is not set.');
        }

        return $this->_name;
    }

    /**
     * Sets name of event.
     *
     * @param string $name Event name
     *
     * @return \PM\Main\Event\Action\AbstractAction
     */
    public function setName($name) {
        $this->_name = $name;

        return $this;
    }

    /**
     * Gets event data.
     *
     * @return mixed
     */
    public function getData() {
        return $this->_data;;
    }

    /**
     * Sets event data.
     *
     * @param mixed $data Data of event
     *
     * @return \PM\Main\Event\Action\AbstractAction
     */
    public function setData($data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * Gets name of module where was event created.
     *
     * @return string
     */
    public function getModule() {
        return $this->_module;
    }

    /**
     * Sets name of module where was event created.
     *
     * @param string $module Name of module
     *
     * @return \PM\Main\Event\Action\AbstractAction
     */
    public function setModule($module) {
        $this->_module = $module;

        return $this;
    }
}
