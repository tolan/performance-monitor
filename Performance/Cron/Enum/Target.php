<?php

namespace PM\Cron\Enum;

use PM\Main\Abstracts\Enum;
use PM\Search;

/**
 * This script defines enum with taget entities for execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Target extends Enum {
    const SCENARIO      = Search\Enum\Target::SCENARIO;
    const STATISTIC_SET = Search\Enum\Target::STATISTIC_SET;
    const STATISTIC_RUN = Search\Enum\Target::STATISTIC_RUN;
}
