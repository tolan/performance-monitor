<?php

/**
 * This script defines factory class for creating display by request settings.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Display_Factory extends Performance_Profiler_Component_AbstractFactory {

    /**
     * Returns display instance.
     *
     * @return Performance_Profiler_Component_Display_Abstract
     */
    public function getStorage() {
        if ($this->getAttemptId()) {
            $storage = $this->getProvider()->get('Performance_Profiler_Component_Display_MySQL');
            $storage->setMeasureId($this->getAttemptId());
        } else {
            $storage = $this->getProvider()->get('Performance_Profiler_Component_Display_Default');
        }

        return $storage;
    }
}
