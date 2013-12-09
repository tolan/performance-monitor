<?php

/**
 * This script defines profiler gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Gearman_Client extends Performance_Main_Abstract_Gearman_Client {

    /**
     * Returns gearman message instance.
     *
     * @return Performance_Profiler_Gearman_Message
     */
    protected function getMessage() {
        return $this->getProvider()->get('Performance_Profiler_Gearman_Message');
    }
}
