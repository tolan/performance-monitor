<?php

/**
 * This script defines profiler gearman message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Gearman_Message extends Performance_Main_Abstract_Gearman_Message {

    /**
     * Returns name of target worker.
     * 
     * @return string
     */
    public function getTarget() {
        return 'Performance_Profiler_Gearman_Worker';
    }
}
