<?php

namespace PM\Cron\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with states of trigger execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class TriggerState extends Enum {
    const IDLE    = 'idle';
    const RUNNING = 'running';
    const DONE    = 'done';
    const ERROR   = 'error';
}
