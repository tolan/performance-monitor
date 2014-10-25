<?php

namespace PM\Statistic\Enum\Run;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with states for run entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class State extends Enum {

    const IDLE    = 'idle';
    const RUNNING = 'running';
    const DONE    = 'done';
    const ERROR   = 'error';
}
