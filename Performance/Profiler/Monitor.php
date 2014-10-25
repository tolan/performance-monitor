<?php

namespace PM\Profiler;

include_once __DIR__.'/../boot.php';

use PM\Main\Provider;
use PM\Profiler\Monitor\Storage\State;

/**
 * This script defines class of the performance profiler monitor.
 * The monitor measure information about each call (each line of processed code) and transform it
 * into call stack structure with statistics information.
 * Parameters for enable measure are in GET request parameters.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Monitor {

    /**
     * Singleton instance
     *
     * @var \PM\Profiler\Monitor
     */
    private static $_instance = false;

    /**
     * Flag that profiler is enabled
     *
     * @var boolean
     */
    private $_isEnabled;

    /**
     * Flag that monitor function was processed. It means catched calls, analyzed and saved generted stattistics.
     *
     * @var boolean
     */
    private $_isProcessed = false;

    /**
     * Provider instance.
     *
     * @var \PM\Main\Provider
     */
    private $_provider = null;

    /**
     * Facade instance.
     *
     * @var \PM\Profiler\Monitor\Facade
     */
    private $_facade = null;

    /**
     * Contruct method.
     *
     * @param \PM\Main\Provider $provider Provider instance
     *
     * @return void
     */
    private function __construct(Provider $provider = null) {
        if ($provider === null) {
            $provider = Provider::getInstance();
        }

        $this->_provider = $provider;
        $this->_facade   = $this->_provider->get('PM\Profiler\Monitor\Factory\Facade')->getFacade();

        $this->reset();
    }

    /**
     * Method for get singleton instance
     *
     * @return \PM\Profiler\Monitor
     */
    public static function getInstance(Provider $provider = null) {
        if (self::$_instance === false) {
            self::$_instance = new self($provider);
        }

        return self::$_instance;
    }

    /**
     * Reset method. It clean all stored data and sets init values.
     *
     * @return \PM\Profiler\Monitor
     */
    public function reset() {
        $this->_isEnabled   = false;
        $this->_isProcessed = false;

        $this->_facade->reset();

        return $this;
    }

    /**
     * Method for enable measure.
     *
     * @return \PM\Profiler\Monitor
     */
    public function enable() {
        $startKey = Enum\HttpKeys::PROFILER_START;
        $get      = $this->_provider->get('request')->getGet();

        if ($get->has($startKey)) {
            $startValue = strtolower($get->get($startKey));
            if ($startValue == '1' || $startValue == 'true') {
                $execTime = ini_get('max_execution_time');
                ini_set('max_execution_time', $execTime * 10);

                $this->_checkEnable();
                $this->_isEnabled = true;
                $this->_facade->start();
            }
        }

        return $this;
    }

    /**
     * Method to disable measure.
     *
     * @return \PM\Profiler\Monitor
     */
    public function disable() {
        if ($this->_isEnabled === true) {
            $this->_facade->stop();
            $this->_isEnabled = false;
        }

        return $this;
    }

    /**
     * Display overview of measure.
     *
     * @return \PM\Profiler\Monitor
     */
    public function display() {
        $this->_checkEnable();
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
        $this->disable();
        $this->_process();
    }

    /**
     * Process measure. It analyze catched calls, generate statistics and save statistics data.
     *
     * @return \PM\Profiler\Monitor
     */
    private function _process() {
        if ($this->_isProcessed === false && $this->_facade->getState() === State::STATE_TICKED) {
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

