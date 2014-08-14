<?php

namespace PF\Statistic\Enum\View;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with types of data from which can be created statistic.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Data extends Enum {
    const URL     = 'url';
    const METHOD  = 'method';
    const TIME    = 'time';
    const CALLS   = 'calls';
    const FILE    = 'file';
    const CONTENT = 'content';
}
