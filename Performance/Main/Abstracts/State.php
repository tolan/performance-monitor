<?php

namespace PF\Main\Abstracts;

use PF\Main;

/**
 * Abstract class for state automat object.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class State implements Main\Interfaces\State {

    /**
     * Actual state.
     *
     * @var mixed
     */
    private $_state;

    /**
     * Transition map for each state.
     *
     * @var array
     */
    private $_map = array();

    /**
     * Construct method.
     *
     * @param mixed $initState     Initial state
     * @param array $transitionMap Transitional map
     *
     * @throws Main\Exception
     *
     * @return void
     */
    public function __construct($initState, $transitionMap = array()) {
        if (!$initState) {
            throw new Main\Exception('Init state must be set');
        }

        $this->_state = $initState;

        if (empty($transitionMap) || !is_array($transitionMap)) {
            throw new Main\Exception('Transition map cannot be empty array');
        }

        $this->_map = $transitionMap;
    }

    /**
     * Sets new state.
     *
     * @param mxed $state New State
     *
     * @throws Main\Exception
     */
    public function setState($state) {
        if (!isset($this->_map[$this->_state])) {
            throw new Main\Exception('Actual state has not follwing state.');
        }

        if (!in_array($state, $this->_map[$this->_state])) {
            throw new Main\Exception('State "'.$state.'" is not allowed.');
        }

        $this->_state = $state;
    }

    /**
     * Gets actual state.
     *
     * @return mixed
     */
    public function getState() {
        return $this->_state;
    }

    /**
     * It checks that the actual state is in given states.
     *
     * @param array $states List of checked states
     *
     * @return \PF\Main\Abstracts\State
     *
     * @throws Main\Exception Throws when actual state is not in checked states.
     */
    public function checkInState($states = array()) {
        if (!in_array($this->_state, (array)$states)) {
            throw new Main\Exception('Storage is not in "'.join('", "', (array)$states).'" state.');
        }

        return $this;
    }
}
