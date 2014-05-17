<?php

namespace PF\Profiler\Monitor\Enum;

use PF\Main\Abstracts;

/**
 * This script defines enum with all possible attributes for call.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class CallAttributes extends Abstracts\Enum {
    const ID             = 'id';
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
    const PARENT         = 'parentId';
    const MEASURE_ID     = 'measureId';
}
