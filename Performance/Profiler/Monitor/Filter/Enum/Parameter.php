<?php

namespace PF\Profiler\Monitor\Filter\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with parameters for filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Parameter extends Enum {
    const FILE      = 'file';
    const LINE      = 'line';
    const IMMERSION = 'immersion';
    const SUB_STACK = 'subStack';
}
