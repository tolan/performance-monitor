<?php

namespace PF\Search\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with all posible format type for entity attrbutes.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Format extends Enum {
    const INT      = 'int';
    const FLOAT    = 'float';
    const DATETIME = 'datetime';
}
