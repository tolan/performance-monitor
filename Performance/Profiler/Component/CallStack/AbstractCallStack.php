<?php

namespace PF\Profiler\Component\CallStack;

use PF\Main\Provider;

/**
 * Abstract class for profiler call stack. Each call stck take all calls and transform it to tree.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractCallStack {

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
        $this->init();
    }

    /**
     * Optional init function instead of constructor.
     *
     * @return void
     */
    protected function init() {}

    /**
     * Returns provider instance.
     *
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
