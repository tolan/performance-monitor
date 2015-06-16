<?php

namespace PM\Settings\Gearman\Control;

/**
 * This script defines class for keep operation mode.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Keep extends AbstractControl {

    /**
     * Execute control operation for mode KEPP.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return Keep
     */
    public function control($status, $worker) {
        $status['keepCount'] = $status['requested'];

        $operation = $this->getOperation();
        $operation->keep($status, $worker);

        return $this;
    }
}
