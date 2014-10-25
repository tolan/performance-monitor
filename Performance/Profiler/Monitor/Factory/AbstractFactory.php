<?php

namespace PM\Profiler\Monitor\Factory;

use PM\Profiler\Monitor\Enum\Type;
use PM\Profiler\Monitor\Enum\HttpKeys;
use PM\Profiler\Monitor\Exception;
use PM\Main\Provider;
use PM\Main\Config;
use PM\Main\Web\Component\Request;

/**
 * This script defines abstract factory class for other factories in PM\Monitor\Factory.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractFactory {

    /**
     * Request instance.
     *
     * @var \PM\Main\Web\Component\Request
     */
    private $_request = null;

    /**
     * Config instance.
     *
     * @var \PM\Main\Config
     */
    private $_config = null;

    /**
     * Provider instance.
     *
     * @var \PM\Main\Provider
     */
    private $_provider = null;

    /**
     * Construct method.
     *
     * @param \PM\Main\Web\Component\Request $request  Web request instance
     * @param \PM\Main\Config                $config   Config instance
     * @param \PM\Main\Provider              $provider Provider instance
     *
     * @return void
     */
    public function __construct(Request $request, Config $config, Provider $provider) {
        $this->_request  = $request;
        $this->_config   = $config;
        $this->_provider = $provider;
    }

    /**
     * Returns config instance.
     *
     * @return \PM\Main\Config
     */
    protected function getConfig() {
        return $this->_config;
    }

    /**
     * Returns provider instance.
     *
     * @return \PM\Main\Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Returns web request instance.
     *
     * @return \PM\Main\Web\Component\Request
     */
    protected function getRequest() {
        return $this->_request;
    }

    /**
     * Returns parameters from GET part of request.
     *
     * @return array
     */
    protected function getParams() {
        $params = array();
        $get    = $this->getRequest()->getGet();

        $params[HttpKeys::MEASURE_ID] = $get->has(HttpKeys::MEASURE_ID) ? $get->get(HttpKeys::MEASURE_ID) : uniqid();

        if ($get->has(HttpKeys::REQUEST_ID)) {
            $params[HttpKeys::REQUEST_ID] = $get->get(HttpKeys::REQUEST_ID);
        }

        return $params;
    }

    /**
     * Returns type of measure storage.
     *
     * @return enum One of enum \PM\Profiler\Monitor\Enum\Type
     *
     * @throws Exception Throws when type is MySQL and measure ID is not set.
     */
    protected function getType() {
        $get    = $this->getRequest()->getGet();
        $config = $this->getConfig()->get('profiler');

        $defType = isset($config['type']) ? $config['type'] : self::DEFAULT_TYPE;
        $type    = $get->has(HttpKeys::TYPE) ? $get->get(HttpKeys::TYPE) : $defType;

        if ($type === Type::MYSQL && !$get->has(HttpKeys::MEASURE_ID)) {
            throw new Exception('MySQL type need set measure ID.');
        }

        return $type;
    }
}
