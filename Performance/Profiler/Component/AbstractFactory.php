<?php

/**
 * Abstract class for all abstract factory component of profiler.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class Performance_Profiler_Component_AbstractFactory {

    /**
     * Provider instnace.
     *
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * ID of attempt.
     *
     * @var int
     */
    private $_attemptId = null;

    /**
     * Construct method.
     *
     * @param Performance_Main_Provider $provider
     */
    public function __construct(Performance_Main_Provider $provider) {
        $this->_provider = $provider;

        $get          = $provider->get('Performance_Main_Web_Component_Request')->getGet();
        $measureIdKey = Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID;

        if ($get->has($measureIdKey)) {
            $this->_attemptId = $get->get($measureIdKey);
        }
    }

    /**
     * Get ID of attempt (it is for MYSQL usage).
     *
     * @return int
     */
    protected function getAttemptId() {
        return $this->_attemptId;
    }

    /**
     * Returns provider instance.
     *
     * @return Performance_Main_Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }
}
