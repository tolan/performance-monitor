<?php

namespace PF\Search\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with all posible target entities.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Target extends Enum {
    const MEASURE = 'measure';
    const TEST    = 'test';
    const ATTEMPT = 'attempt';
}
