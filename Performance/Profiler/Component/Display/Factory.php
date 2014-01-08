<?php

namespace PF\Profiler\Component\Display;

use PF\Profiler\Component\AbstractFactory;

/**
 * This script defines factory class for creating display by request settings.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Factory extends AbstractFactory {

    /**
     * Returns display instance.
     *
     * @return \PF\Profiler\Component\Display\AbstractDisplay
     */
    public function getStorage() {
        if ($this->getAttemptId()) {
            $storage = $this->getProvider()->get('PF\Profiler\Component\Display\MySQL');
            $storage->setMeasureId($this->getAttemptId());
        } else {
            $storage = $this->getProvider()->get('PF\Profiler\Component\Display\Browser');
        }

        return $storage;
    }
}
