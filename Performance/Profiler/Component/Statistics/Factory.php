<?php

namespace PF\Profiler\Component\Statistics;

use PF\Profiler\Component\AbstractFactory;

/**
 * This script defnes factory class for creating profler statistic instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Factory extends AbstractFactory {

    /**
     * Returns instance of profiler statistic.
     *
     * @return \PF\Profiler\Component\Statistics\Abstract
     */
    public function getStatistics() {
        if ($this->getAttemptId()) {
            $storage = $this->getProvider()->get('PF\Profiler\Component\Statistics\MySQL');
            $storage->setAttemptId($this->getAttemptId());
        } else {
            $storage = $this->getProvider()->get('PF\Profiler\Component\Statistics\Browser');
        }

        return $storage;
    }
}
