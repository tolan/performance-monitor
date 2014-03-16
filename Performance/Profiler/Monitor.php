<?php

namespace PF\Profiler;

include_once __DIR__.'/../boot.php';

use PF\Main\Provider;

/**
 * This script defines class of the performance profiler.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Monitor {

    /**
     * Singleton instance
     *
     * @var \PF\Profiler\Monitor
     */
    private static $_instance = false;

    /**
     * Flag that profiler is enabled
     *
     * @var boolean
     */
    private $_isEnabled;

    /**
     * Flag that moniter function is processed. It means catched calls, analyzed and saved generted stattistics.
     *
     * @var boolean
     */
    private $_isProcessed = false;

    /**
     * Provider instance.
     *
     * @var \PF\Main\Provider
     */
    private $_provider = null;

    /**
     * Facade instance for profiler monitor api components.
     *
     * @var \PF\Profiler\Main\Facade
     */
    private $_facade;

    /**
     * Contruct method.
     *
     * @param \PF\Main\Provider $provider
     */
    private function __construct(Provider $provider = null) {
        if ($provider === null) {
            $provider = Provider::getInstance();
        }

        $this->reset();
        $this->_provider = $provider;
    }

    /**
     * Method for get singleton instance
     *
     * @return \PF\Profiler\Monitor
     */
    public static function getInstance(Provider $provider = null) {
        if (self::$_instance === false) {
            self::$_instance = new self($provider);
        }

        return self::$_instance;
    }

    /**
     * Reset method.
     *
     * @return \PF\Profiler\Monitor
     */
    public function reset() {
        $this->_facade      = null;
        $this->_isEnabled   = false;
        $this->_isProcessed = false;

        return $this;
    }

    /**
     * Method for enable profiling.
     *
     * @return \PF\Profiler\Monitor
     */
    public function enable() {
        $this->_facade = $this->_provider->get('PF\Profiler\Main\Factory\Facade')->getFacade();

        $startKey = Enum\HttpKeys::PROFILER_START;
        $get      = $this->_provider->get('request')->getGet();

        if ($get->has($startKey) && strtolower($get->get($startKey)) == '1') {
            $this->_checkEnable();
            $this->_isEnabled = true;
            $this->_facade->start();
        }

        return $this;
    }

    /**
     * Method to disable profiling.
     *
     * @return \PF\Profiler\Monitor
     */
    public function disable() {
        if ($this->_isEnabled === true) {
            $this->_isEnabled = false;

            $this->_facade->stop();
        }

        return $this;
    }

    /**
     * Display overview of measure.
     *
     * @return \PF\Profiler\Monitor
     */
    public function display() {
        $this->_process();
        $this->_facade->display();

        return $this;
    }

    /**
     * Destruct instance.
     *
     * @return void
     */
    public function __destruct() {
        $this->_process();
    }

    /**
     * Process measure. It analyze catched calls, generate statistics and save statistics data.
     *
     * @return \PF\Profiler\Monitor
     */
    private function _process() {
        if ($this->_isProcessed === false) {
            $this->_facade->analyzeCallStack();
            $this->_facade->generateStatistics();
            $this->_facade->saveStatistics();
            $this->_isProcessed = true;
        }

        return $this;
    }

    /**
     * Checks that profile is enabled and throws exception if it is not enabled.
     *
     * @throws Exception
     *
     * @return void
     */
    private function _checkEnable() {
        if ($this->_isEnabled === true) {
            throw new Exception('Profiler is still enabled.');
        }
    }
}

