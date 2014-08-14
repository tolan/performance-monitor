<?php

namespace PF\Statistic\Enum\View;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with types of functions which can be aplicable to data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Functions extends Enum {
    const AVERAGE = 'avg';
    const MAX     = 'max';
    const MEDIAN  = 'median';
    const SUM     = 'sum';
    const COUNT   = 'count';
}
