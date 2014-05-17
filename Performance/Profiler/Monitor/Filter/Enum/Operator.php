<?php

namespace PF\Profiler\Monitor\Filter\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with all possible operators for filters.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Operator extends Enum {
    const REG_EXP     = 'regExp';
    const LOWER_THAN  = 'lowerThan';
    const HIGHER_THAN = 'higherThan';
    const BOOLEAN     = 'boolean';
}
