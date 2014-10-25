<?php

namespace PM\Profiler\Enum;

use PM\Main\Abstracts\Enum;

/**
 * This script defines enum with states of attempt.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class TestState extends Enum {

    const STATE_IDLE           = 'idle';
    const STATE_MEASURE_ACTIVE = 'measure_active';
    const STATE_DONE           = 'done';
    const STATE_ERROR          = 'error';

    /**
     * Return non-error states.
     *
     * @return array
     */
    public static function getStates() {
        return array(
            self::STATE_IDLE,
            self::STATE_MEASURE_ACTIVE,
            self::STATE_DONE
        );
    }
}
