<?php

namespace PF\Profiler\Main\Factory;

use PF\Main\Provider;
use PF\Main\Web\Component\Request;

/**
 * This script defines abstract factory class for other factories in PF\Main\Factory.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractFactory {
    const TYPE_CACHE = 'Cache';
    const TYPE_MYSQL = 'MySQL';

    /**
     * Request instance.
     *
     * @var \PF\Main\Web\Component\Request
     */
    private $_request = null;

    /**
     * Provider instance.
     *
     * @var \PF\Main\Provider
     */
    private $_provider = null;

    /**
     * Construct method.
     *
     * @param \PF\Main\Web\Component\Request $request  Web request instance
     * @param \PF\Main\Provider              $provider Provider instance
     *
     * @return void
     */
    public function __construct(Request $request, Provider $provider) {
        $this->_request  = $request;
        $this->_provider = $provider;
    }

    /**
     * Returns provider instance.
     *
     * @return \PF\Main\Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Returns web request instance.
     *
     * @return \PF\Main\Web\Component\Request
     */
    protected function getRequest() {
        return $this->_request;
    }
}
