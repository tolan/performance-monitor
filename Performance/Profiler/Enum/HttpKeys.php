<?php

namespace PF\Profiler\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with keys for http GET request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class HttpKeys extends Enum {

    const PROFILER_START = 'PROFILER_ENABLED';
    const MEASURE_ID     = 'MEASURE_ID';
    const TEST_ID        = 'TEST_ID';
    const ATTEMPT_ID     = 'ATTEMPT_ID';
}
