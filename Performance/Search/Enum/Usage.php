<?php

namespace PM\Search\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with all posible usage of template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Usage extends Enum {
    const SEARCH    = 'search';
    const STATISTIC = 'statistic';
    const CRON      = 'cron';
}
