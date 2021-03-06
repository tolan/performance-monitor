<?php

namespace PM\Search\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with all posible filter types.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Type extends Enum {
    const QUERY  = 'query';
    const STRING = 'string';
    const DATE   = 'date';
    const ENUM   = 'enum';
    const INT    = 'int';
    const FLOAT  = 'float';
}
