<?php

namespace PF\Profiler\Main;

use PF\Main\Abstracts;
use PF\Main\Traits;

class Storage extends Abstracts\ArrayAccessIterator implements Interfaces\Storage {

    use Traits\Observable;

    private $_repository;
    private $_state;
    private $_call;

    public function __construct(Interfaces\Repository $repository, Interfaces\Call $call, Storage\State $state) {
        $this->_repository = $repository;
        $this->_state      = $state;
        $this->_call       = $call;
    }

    public function getCallInstance() {
        return $this->_call;
    }

    public function getState() {
        return $this->_state->getState();
    }

    public function setState($state) {
        $this->_state->setState($state);

        return $this;
    }

    public function getCalls() {
        $this->_state->checkInState(Storage\State::STATE_TICKED);

        return $this->_data;
    }

    public function saveCalls() {
        $this->_state->checkInState(Storage\State::STATE_TICKED);

        $this->_repository->saveCalls($this);

        return $this;
    }

    public function getCallStack() {
        $this->_state->checkInState(Storage\State::STATE_ANALYZED);

        return $this->_data;
    }

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

    public function saveStatistics() {
        $this->_state->checkInState(Storage\State::STATE_STAT_GENERATED);

        $this->_repository->saveCallFlyweight($this->_call);
        $this->_repository->saveCallStatistics($this);

        return $this;
    }
}
