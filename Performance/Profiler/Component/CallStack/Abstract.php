<?php

/**
 * Abstract class for profiler call stack. Each call stck take all calls and transform it to tree.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class Performance_Profiler_Component_CallStack_Abstract {

    /**
     * Provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param Performance_Main_Provider $provider Provider instance
     *
     */
    final public function __construct(Performance_Main_Provider $provider) {
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
     * @return Performance_Main_Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
