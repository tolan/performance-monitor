<?php

namespace PM\Search\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with all posible target entities.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Target extends Enum {
    const SCENARIO = 'scenario';
    const TEST     = 'test';
    const MEASURE  = 'measure';
    const CALL     = 'call';
}
