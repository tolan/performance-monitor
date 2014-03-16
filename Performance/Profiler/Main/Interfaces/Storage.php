<?php

namespace PF\Profiler\Main\Interfaces;

use PF\Main\Interfaces;
use PF\Profiler\Main\Storage\State;

interface Storage extends Interfaces\Iterator, Interfaces\ArrayAccess, Interfaces\Observable {

    public function __construct(Repository $repository, Call $call, State $state);

    public function getCallInstance();

    public function getState();
    public function setState($state);

    public function saveCalls();
    public function getCalls();

    public function saveStatistics();
    public function getStatistics();
}