<?php

namespace PF\Profiler\Enum;

use PF\Main\Abstracts\Enum;

/**
 * This script defines enum with states of attempt.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class AttemptState extends Enum {

    const STATE_IDLE                 = 'idle';
    const STATE_MEASURE_ACTIVE       = 'measure_active';
    const STATE_MEASURED             = 'measured';
    const STATE_ANALYZE_ACTIVE       = 'analyze_active';
    const STATE_ANALYZED             = 'analyzed';
    const STATE_STATISTIC_GENERATING = 'statistic_generating';
    const STATE_STATISTIC_GENERATED  = 'statistic_generated';
    const STATE_ERROR                = 'error';

    /**
     * Return non-error states.
     *
     * @return array
     */
    public static function getStates() {
        return array(
            self::STATE_IDLE,
            self::STATE_MEASURE_ACTIVE,
            self::STATE_MEASURED,
            self::STATE_ANALYZE_ACTIVE,
            self::STATE_ANALYZED,
            self::STATE_STATISTIC_GENERATING,
            self::STATE_STATISTIC_GENERATED
        );
    }
}
