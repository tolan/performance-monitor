<?php

namespace PF\Profiler\Gearman;

/**
 * This script defines profiler gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Client extends \PF\Main\Abstracts\Gearman\Client {

    /**
     * Returns gearman message instance.
     *
     * @return \PF\Profiler\Gearman\Message
     */
    protected function getMessage() {
        return $this->getProvider()->get('PF\Profiler\Gearman\Message');
    }
}
