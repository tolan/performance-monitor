<?php

namespace PM\Profiler\Monitor\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with keys for http GET request.
 * TODO merge with PM\Profiler\Enum\HttpKeys
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class HttpKeys extends Enum {

    const PROFILER_START = 'PROFILER_ENABLED'; // required
    const TYPE           = 'TYPE';             // optional
    const MEASURE_ID     = 'MEASURE_ID';       // optional
    const REQUEST_ID     = 'REQUEST_ID';       // optional - for set filters

    const TEST_ID = 'TEST_ID'; // constant for gearman
}
