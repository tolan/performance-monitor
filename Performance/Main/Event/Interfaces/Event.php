<?php

namespace PM\Main\Event\Interfaces;

/**
 * This script defines interface for event action class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Event extends Message {

    /**
     * Gets name of event.
     *
     * @return string
     */
    public function getName();

    /**
     * Sets name of event.
     *
     * @param string $name Event name
     */
    public function setName($name);

    /**
     * Gets event data.
     */
    public function getData();

    /**
     * Sets event data.
     *
     * @param mixed $data Data of event
     */
    public function setData($data);

    /**
     * Gets name of module where was event created.
     */
    public function getModule();

    /**
     * Sets name of module where was event created.
     *
     * @param string $module Name of module
     */
    public function setModule($module);
}
