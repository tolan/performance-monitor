<?php

namespace PM\Settings\Gearman\Control;

/**
 * This script defines class for manual operation mode.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Manual extends AbstractControl {

    /**
     * Execute control operation for mode MANUAL.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return Manual
     */
    public function control($status, $worker) {
        $count  = abs($status['requested']);
        $method = $status['requested'] > 0 ? 'start' : 'stop';

        $operation = $this->getOperation();
        for($i = 0; $i < $count; $i++) {
            $operation->$method($status, $worker);
        }

        return $this;
    }
}
