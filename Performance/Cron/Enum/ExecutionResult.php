<?php

namespace PM\Cron\Enum;

/**
 * This script defines enum with results of cron execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class ExecutionResult {
    const SUCCESS = 'success';
    const PARTIAL = 'partial';
    const ERROR   = 'error';
    const FATAL   = 'fatal';
}
