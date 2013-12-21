<?php

/**
 * This script defines enum with keys for http GET request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Profiler_Enum_HttpKeys extends Performance_Main_Abstract_Enum {
    const PROFILER_START = 'PROFILER_ENABLED';
    const MEASURE_ID     = 'MEASURE_ID';
    const TEST_ID        = 'TEST_ID';
    const ATTEMPT_ID     = 'ATTEMPT_ID';
}
