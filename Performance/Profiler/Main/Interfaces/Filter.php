<?php

namespace PF\Profiler\Main\Interfaces;

interface Filter {
    public function __construct($filterData);
    public function isAllowedBacktrace($backtrace);
}
