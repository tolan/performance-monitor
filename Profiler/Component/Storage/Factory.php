<?php

/**
 * This script defnes factory class for creating profler storage instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Storage_Factory extends Performance_Profiler_Component_AbstractFactory {

    /**
     * Returns profiler storage instance.
     *
     * @return Performance_Profiler_Component_Storage_Abstract
     */
    public function getStorage() {
        if ($this->getAttemptId()) {
            $storage = $this->getProvider()->get('Performance_Profiler_Component_Storage_MySQL');
            $storage->setAttemptId($this->getAttemptId());
        } else {
            $storage = $this->getProvider()->get('Performance_Profiler_Component_Storage_Default');
        }

        return $storage;
    }
}
