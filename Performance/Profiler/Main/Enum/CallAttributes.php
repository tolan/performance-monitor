<?php

namespace PF\Profiler\Main\Enum;

use PF\Main\Abstracts;

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
}
