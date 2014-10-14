<?php

namespace PF\Statistic\Gearman;

/**
 * This script defines statistic gearman message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Message extends \PF\Main\Abstracts\Gearman\Message {

    /**
     * Returns name of target worker.
     *
     * @return string
     */
    public function getTarget() {
        return 'PF\Statistic\Gearman\Worker';
    }
}
