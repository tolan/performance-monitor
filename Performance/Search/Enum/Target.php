<?php

namespace PF\Search\Enum;

/**
 * This script defines enum with all posible target entities.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Target extends \Performance_Main_Abstract_Enum {
    const MEASURE = 'measure';
    const TEST    = 'test';
    const ATTEMPT = 'attempt';
}
