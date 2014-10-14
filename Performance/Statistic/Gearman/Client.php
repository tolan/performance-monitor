<?php

namespace PF\Statistic\Gearman;

/**
 * This script defines statistic gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Client extends \PF\Main\Abstracts\Gearman\Client {

    /**
     * Returns gearman message instance.
     *
     * @return \PF\Statistic\Gearman\Message
     */
    protected function getMessage() {
        return $this->getProvider()->get('PF\Statistic\Gearman\Message');
    }
}
