<?php

namespace PF\Profiler\Component\Storage;

use PF\Profiler\Enum\CallParameters;

/**
 * This script defines profiler storage class for direct access from browser.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Browser extends AbstractStorage {
    
    public function save() {
        $this->getProvider()->get('PF\Profiler\Component\CallStack\Browser')->setStorage($this);
    }
}
