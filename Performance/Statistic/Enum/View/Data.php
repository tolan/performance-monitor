<?php

namespace PM\Statistic\Enum\View;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with types of data from which can be created statistic.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Data extends Enum {
    const CALLS   = 'calls';
    const CONTENT = 'content';
    const FILE    = 'file';
    const METHOD  = 'method';
    const TIME    = 'time';
    const URL     = 'url';
}
