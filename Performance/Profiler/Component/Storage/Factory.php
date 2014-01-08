<?php

namespace PF\Profiler\Component\Storage;

use PF\Profiler\Component\AbstractFactory;

/**
 * This script defnes factory class for creating profler storage instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Factory extends AbstractFactory {

    /**
     * Returns profiler storage instance.
     *
     * @return \PF\Profiler\Component\Storage\AbstractStorage
     */
    public function getStorage() {
        if ($this->getAttemptId()) {
            $storage = $this->getProvider()->get('PF\Profiler\Component\Storage\MySQL');
            $storage->setAttemptId($this->getAttemptId());
        } else {
            $storage = $this->getProvider()->get('PF\Profiler\Component\Storage\Browser');
        }

        return $storage;
    }
}
