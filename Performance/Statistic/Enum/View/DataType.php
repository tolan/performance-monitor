<?php

namespace PM\Statistic\Enum\View;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with types of data type. It defines posibles types of data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class DataType extends Enum {
    const ENUM      = 'enum';
    const REG_EXP   = 'reg_exp';
    const RANGE_INT = 'range_int';
}
