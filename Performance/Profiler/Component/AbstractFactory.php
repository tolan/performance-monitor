<?php

namespace PF\Profiler\Component;

use PF\Main\Provider;
use PF\Profiler\Enum\HttpKeys;

/**
 * Abstract class for all abstract factory component of profiler.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractFactory {

    /**
     * Provider instnace.
     *
     * @var \PF\Main\Provider
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
     * @param \PF\Main\Provider $provider
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Get ID of attempt (it is for MYSQL usage).
     *
     * @return int
     */
    protected function getAttemptId() {
        $this->_attemptId = null;

        $get          = $this->_provider->get('request')->getGet();
        $measureIdKey = HttpKeys::ATTEMPT_ID;

        if ($get->has($measureIdKey)) {
            $this->_attemptId = $get->get($measureIdKey);
        }

        return $this->_attemptId;
    }

    /**
     * Returns provider instance.
     *
     * @return \PF\Main\Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }
}
