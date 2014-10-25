<?php

namespace PM\Profiler\Monitor;

use PM\Main\Abstracts;
use PM\Main\Traits;

/**
 * This script defines class for monitor storage.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Storage extends Abstracts\ArrayAccessIterator implements Interfaces\Storage {

    use Traits\Observable;

    /**
     * Monitor repository instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Repository
     */
    private $_repository;

    /**
     * Monitor call fly weight.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Call
     */
    private $_call;

    /**
     * Monitor storage state instance.
     *
     * @var \PM\Profiler\Monitor\Storage\State
     */
    private $_state;

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Repository $repository Monitor repository instnace
     * @param \PM\Profiler\Monitor\Interfaces\Call       $call       Monitor call fly weight
     * @param \PM\Profiler\Monitor\Storage\State         $state      Monitor storage state instance (it is for manage states)
     *
     * @return void
     */
    public function __construct(Interfaces\Repository $repository, Interfaces\Call $call, Storage\State $state) {
        $this->_repository = $repository;
        $this->_state      = $state;
        $this->_call       = $call;
    }

    /**
     * Returns monitor call fly weight instance.
     *
     * @return \PM\Profiler\Monitor\Interfaces\Call
     */
    public function getCallInstance() {
        return $this->_call;
    }

    /**
     * Returns actual state of storage.
     *
     * @return enum \PM\Profiler\Monitor\Storage\State
     */
    public function getState() {
        return $this->_state->getState();
    }

    /**
     * Sets new state of storage.
     *
     * @param enum $state One of \PM\Profiler\Monitor\Storage\State
     *
     * @return \PM\Profiler\Monitor\Storage
     */
    public function setState($state) {
        $this->_state->setState($state);

        $this->notify();

        return $this;
    }

    /**
     * Returns statistics data. If storage is empty then it loads data from repository.
     *
     * @return array
     */
    public function getStatistics() {
        if ($this->getState() === Storage\State::STATE_EMPTY) {
            $this->setState(Storage\State::STATE_TICKED);
            $this->setState(Storage\State::STATE_ANALYZED);
            $this->_repository->loadCallFlyweight();
            $this->_repository->loadCallStatistics($this);
        }

        $this->_state->checkInState(Storage\State::STATE_STAT_GENERATED);

        return $this->_data;
    }

    /**
     * It saves all data in storage into repository. State must be STATE_STAT_GENERATED.
     *
     * @return \PM\Profiler\Monitor\Storage
     */
    public function saveStatistics() {
        $this->_state->checkInState(Storage\State::STATE_STAT_GENERATED);

        $this->_repository->reset();
        $this->_repository->saveCallFlyweight($this->_call);
        $this->_repository->saveCallStatistics($this);

        return $this;
    }

    /**
     * Clean all stored data in storage and repository.
     *
     * @reutrn \PM\Profiler\Monitor\Storage
     */
    public function reset() {
        $this->fromArray(array());
        $this->setState(Storage\State::STATE_EMPTY);
        $this->_repository->reset();

        return $this;
    }
}
