<?php

/**
 * This script defines enum class for log level.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Log_Enum_Level extends Performance_Main_Abstract_Enum {
    const TRACE   = 0;
    const DEBUG   = 1;
    const INFO    = 2;
    const WARNING = 3;
    const ERROR   = 4;
    const FATAL   = 5;
    const OFF     = 6;
}
