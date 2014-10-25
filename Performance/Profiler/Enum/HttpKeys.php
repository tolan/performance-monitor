<?php

namespace PM\Profiler\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with keys for http GET request.
 * TODO merge with PM\Profiler\Monitor\Enum\HttpKeys
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class HttpKeys extends Enum {

    const PROFILER_START = 'PROFILER_ENABLED';
    const MEASURE_ID     = 'MEASURE_ID';
    const TEST_ID        = 'TEST_ID';
    const ATTEMPT_ID     = 'ATTEMPT_ID';
}
