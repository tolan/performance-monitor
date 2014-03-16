<?php

namespace PF\Profiler\Component\CallStack;

use PF\Profiler\Component\Storage\AbstractStorage;

/**
 * This script defines default profiler call stack. It is for direct access from browser.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Browser extends AbstractCallStack {
    
    /**
     * Storage instance.
     * 
     * @var \PF\Profiler\Component\Storage\AbstractStorage
     */
    private $_storage = null;

    /**
     * Sets storage for analyzing.
     * 
     * @param \PF\Profiler\Component\Storage\AbstractStorage $storage Storage instance
     * 
     * @return \PF\Profiler\Component\CallStack\Browser
     */
    public function setStorage(AbstractStorage $storage) {
        $this->_storage = $storage;

        return $this;
    }
    
    /**
     * Returns array with calls.
     *
     * @return array
     */
    protected function getStorageData() {
        return $this->_storage->getStorageCalls();
    }
}