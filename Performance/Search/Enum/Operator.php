<?php

namespace PF\Search\Enum;

/**
 * This script defines enum with all posible operators.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Operator extends \Performance_Main_Abstract_Enum {
    const IS_EMPTY = 'empty';
    const IS_SET   = 'set';

    // string
    const CONTAINS          = 'contains';
    const DOES_NOT_CONTAINS = 'does not contains';

    // date
    const AFTER = 'after';
    const BEFORE = 'before';

    //enum
    const IN = 'in';
    const NOT_IN = 'not in';

    // int
    const EQUAL        = 'equal';
    const NOT_EQUAL    = 'not equal';
    // float
    const GREATER_THAN = 'greater than';
    const LESS_THAN    = 'less than';
}
