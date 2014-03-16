<?php

namespace PF\Profiler\Main\Interfaces;

interface Analyzator {
    //mediator sender

    public function __construct(Storage $storage);
    public function analyze();
}
