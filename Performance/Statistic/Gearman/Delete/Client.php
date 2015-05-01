<?php

namespace PM\Statistic\Gearman\Delete;

/**
 * This script defines statistic gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Client extends \PM\Main\Abstracts\Gearman\Client {

    /**
     * Returns gearman message instance.
     *
     * @return Message
     */
    protected function getMessage() {
        return $this->getProvider()->get('PM\Statistic\Gearman\Delete\Message');
    }
}
