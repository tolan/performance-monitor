<?php

namespace PF\Profiler\Main\Interfaces;

interface Statistic {
    //mediator sender
    //
    public function __construct(Storage $storage);
    public function generate();
}
