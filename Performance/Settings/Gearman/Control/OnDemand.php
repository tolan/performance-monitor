<?php

namespace PM\Settings\Gearman\Control;

/**
 * This script defines class for on demand operation mode.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class OnDemand extends AbstractControl {

    /**
     * Execute control operation for mode ON_DEMAND.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return OnDemand
     */
    public function control($status, $worker) {
        $max     = max($status['requested'] - $status['available'], 0);
        $toStart = min($max, ($status['queue'] - $status['available']));

        $operation = $this->getOperation();

        if ($status['requested'] === 0) {
            $operation->stopAll($status, $worker);
        } elseif ($toStart > 0) {
            for($i = 0; $i < $toStart; $i++) {
                $operation->start($status, $worker);
            }
        }

        return $this;
    }
}
