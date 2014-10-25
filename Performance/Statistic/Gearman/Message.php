<?php

namespace PM\Statistic\Gearman;

/**
 * This script defines statistic gearman message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Message extends \PM\Main\Abstracts\Gearman\Message {

    /**
     * Returns name of target worker.
     *
     * @return string
     */
    public function getTarget() {
        return 'PM\Statistic\Gearman\Worker';
    }
}
