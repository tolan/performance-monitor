<?php

namespace PF\Profiler\Main;

class Filter implements Interfaces\Filter {

    public function __construct($filterData) {
    }

    public function isAllowedBacktrace($backtrace) {
        return true;
    }
}
