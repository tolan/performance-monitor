<?php

namespace PM\Cron\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with cron actions.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Action extends Enum {
    const RUN    = 'run';
    const DELETE = 'delete';
}
