<?php

namespace PF\Main\Interfaces;

/**
 * Interface for state objects.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface State {

    /**
     * Returns actual state.
     *
     * @return mixed
     */
    public function getState();

    /**
     * Sets new state.
     *
     * @param mixed $state New state
     */
    public function setState($state);
}
