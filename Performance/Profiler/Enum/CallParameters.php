<?php

namespace PF\Profiler\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with states of attempt.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class CallParameters extends Enum {
    
    const START_TIME     = 'start';
    const END_TIME       = 'end';
    const IMMERSION      = 'immersion';
    const FILE           = 'file';
    const LINE           = 'line';
    const SUB_STACK      = 'subStack';
    const TIME           = 'time';
    const TIME_SUB_STACK = 'timeSubStack';
    const CONTENT        = 'content';
    const LINES          = 'lines';
}
