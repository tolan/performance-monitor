<?php

namespace PF\Statistic\Enum\Source;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with types of template source.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Type extends Enum {
    const TEMPLATE = 'template';
    const ALL      = 'all';
    const MANUAL   = 'manual';
}
