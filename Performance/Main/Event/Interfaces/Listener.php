<?php

namespace PM\Main\Event\Interfaces;

/**
 * This script defines interface for event listener.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Listener {

    /**
     * Gets name of event.
     */
    public function getName();

    /**
     * Sets name of event.
     *
     * @param string $name Name of event
     */
    public function setName($name);

    /**
     * Get closure function.
     */
    public function getClosure();

    /**
     * Sets closure function.
     *
     * @param \Closure $closure Instance of closure function
     */
    public function setClosure(\Closure $closure);
}
