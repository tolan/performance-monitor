<?php

namespace PM\Profiler\Monitor\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum of repository and display types.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Type extends Enum {
    // keep in lower case for REST API uri
    const SESSION = 'session';
    const FILE    = 'file';
    const MYSQL   = 'mysql';
}
