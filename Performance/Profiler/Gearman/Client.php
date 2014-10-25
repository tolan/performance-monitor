<?php

namespace PM\Profiler\Gearman;

/**
 * This script defines profiler gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Client extends \PM\Main\Abstracts\Gearman\Client {

    /**
     * Returns gearman message instance.
     *
     * @return \PM\Profiler\Gearman\Message
     */
    protected function getMessage() {
        return $this->getProvider()->get('PM\Profiler\Gearman\Message');
    }
}
