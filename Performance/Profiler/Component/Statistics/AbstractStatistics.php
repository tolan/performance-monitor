<?php

namespace PF\Profiler\Component\Statistics;

use PF\Main\Provider;

/**
 * Abstract class for profiler statistics.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractStatistics {

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
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
        $this->init();
    }

    /**
     * Optional init method instead of construct.
     *
     * @return void
     */
    protected function init() {}

    /**
     * Gets provider instance.
     *
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
