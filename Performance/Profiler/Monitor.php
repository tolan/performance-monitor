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
     * Provider instance.
     *
     * @var \PF\Main\Provider
     */
    private $_provider = null;

    /**
     * Storage for calls
     *
     * @var \PF\Profiler\Component\Storage\AbstractStorage
     */
    private $_storage = null;

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
        $this->_storage   = null;
        $this->_isEnabled = false;

        return $this;
    }

    /**
     * Method for enable profiling.
     *
     * @return \PF\Profiler\Monitor
     */
    public function enable() {
        if ($this->_storage === null) {
            $this->_storage = $this->_provider->get('PF\Profiler\Component\Storage\Factory')->getStorage();
        }

        $startKey = Enum\HttpKeys::PROFILER_START;
        $get = $this->_provider->get('request')->getGet();

        if ($get->has($startKey) && strtolower($get->get($startKey)) == '1') {
            $this->_checkEnable();
            $this->_isEnabled = true;
            $this->_storage->start();
        }

        return $this;
    }

    /**
     * Method to disable profiling.
     *
     * @return \PF\Profiler\Monitor
     */
    public function disable() {
        if ($this->_isEnabled == false) {
            return $this;
        }

        $this->_isEnabled = false;
        $this->_storage->stop();
        $this->_storage->save();

        return $this;
    }

    /** OLD IMPLEMENTATION **/

    /**
     * Method for display analyzed calls in tree.
     *
     * @return void
     */
//    public function display() {
//        if ($this->_isDisplayed == true) {
//            return ;
//        }
//
//        $additionalInfo = array(
//            'callsCount' => $this->getCallsCount(),
//            'memory' => $this->getMemory(),
//            'memoryPeak' => $this->getMemoryPeak(),
//            'time' => $this->getTime()
//        );
//
//        $start    = microtime(TRUE);
//        $startMem = memory_get_usage();
//
//        $stat = $this->getStatistics();
//
//        $endMemPeak = memory_get_usage() - $startMem;
//        $end        = ((microtime(TRUE)-$start)*1000000);
//
//        $additionalInfo['analyzeTime']    = $end;
//        $additionalInfo['analyzedMemory'] = $endMemPeak;
//        echo Performance_Profiler_Helper_Display::render($stat, $additionalInfo);
//
//        $this->_isDisplayed = true;
//    }

    /**
     * Destruct method which display result when code is ended.
     *
     * @return void
     */
//    public function __destruct() {
//        $this->disable();
//        $this->display();
//    }



    /**
     * Analyze storage into Performance_CallStack tree.
     *
     * @param array $storage Array with registred calls in format by tick method
     *
     * @return Performance_Profiler_CallStack
     */
//    private function _analyzeStorage(array $storage) {
//        $callStack       = new Performance_Profiler_CallStack();
//        $avialableMemory = $this->_convertMemory(ini_get('memory_limit')) * 0.9;
//
//        return $callStack;
//    }

    /**
     * Converts string in giga, mega, kilo - bytes to bytes.
     *
     * @param string $memoryString Memory string
     *
     * @return int
     */
//    private function _convertMemory($memoryString) {
//        if (stristr($memoryString, 'G')) {
//            return (int)strstr($memoryString, 'G', true) * pow(2, 30);
//        } elseif (stristr($memoryString, 'M')) {
//            return (int)strstr($memoryString, 'M', true) * pow(2, 20);
//        } elseif (stristr($memoryString, 'K')) {
//            return (int)strstr($memoryString, 'K', true) * pow(2, 10);
//        } else {
//            return (int)$memoryString;
//        }
//    }

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

