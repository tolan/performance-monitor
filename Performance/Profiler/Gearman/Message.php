<?php

namespace PM\Profiler\Gearman;

/**
 * This script defines profiler gearman message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Message extends \PM\Main\Abstracts\Gearman\Message {

    /**
     * Returns name of target worker.
     *
     * @return string
     */
    public function getTarget() {
        return 'PM\Profiler\Gearman\Worker';
    }
}
