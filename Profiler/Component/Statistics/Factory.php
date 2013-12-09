<?php

/**
 * This script defnes factory class for creating profler statistic instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Statistics_Factory extends Performance_Profiler_Component_AbstractFactory {

    /**
     * Returns instance of profiler statistic.
     *
     * @return Performance_Profiler_Component_Statistics_Abstract
     */
    public function getStatistics() {
        if ($this->getAttemptId()) {
            $storage = $this->getProvider()->get('Performance_Profiler_Component_Statistics_MySQL');
            $storage->setAttemptId($this->getAttemptId());
        } else {
            $storage = $this->getProvider()->get('Performance_Profiler_Component_Statistics_Default');
        }

        return $storage;
    }
}
