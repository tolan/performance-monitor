<?php

namespace PF\Profiler\Main\Interfaces;

interface Call {
    // fly weight

    public function createCall($backtrace, $startTime, $endTime);
    public function getContent($call);
    public function isCycle($call);
}
