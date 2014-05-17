<?php

namespace PF\Profiler\Monitor\Filter\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with character type (positive or negative).
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Type extends Enum {
    const POSITIVE = 'positive';
    const NEGATIVE = 'negative';
}
