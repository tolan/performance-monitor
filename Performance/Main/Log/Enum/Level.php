<?php

namespace PM\Main\Log\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum class for log level.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Level extends Enum {
    const TRACE   = 0;
    const DEBUG   = 1;
    const INFO    = 2;
    const WARNING = 3;
    const ERROR   = 4;
    const FATAL   = 5;
    const OFF     = 6;
}
