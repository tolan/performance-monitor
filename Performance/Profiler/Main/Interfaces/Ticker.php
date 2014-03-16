<?php

namespace PF\Profiler\Main\Interfaces;

interface Ticker {

    public function __construct (Storage $storage);
    public function addFilter (Filter $filter);
    public function tick();
    public function start();
    public function stop();
    public function isRuning();
}
