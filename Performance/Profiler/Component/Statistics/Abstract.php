<?php

/**
 * Abstract class for profiler statistics.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class Performance_Profiler_Component_Statistics_Abstract {

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
     */
    final public function __construct(Performance_Main_Provider $provider) {
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
     * @return Performance_Main_Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
