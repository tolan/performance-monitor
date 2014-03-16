<?php

namespace PF\Profiler\Component\Display;

use PF\Main\Provider;

/**
 * Abstract class for profiler display.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractDisplay {
    
    /**
     * Provider instance
     *
     * @var \PF\Main\Provider
     */
    private $_provider;
    
    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     *
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }
    
    abstract public function display();
    
    /**
     * Returns provider instance.
     *
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
