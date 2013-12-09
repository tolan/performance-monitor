<?php
include __DIR__.'/../boot.php';

/**
 * This script defines class of the performance profiler.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Monitor {

    /**
     * Singleton instance
     *
     * @var Performance_Profiler_Monitor
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
     * @var Performance_Provider
     */
    private $_provider = null;

    /**
     * Storage for calls
     *
     * @var Performance_Profiler_Component_Storage
     */
    private $_storage = null;

    /**
     * Contruct method.
     *
     * @param Performance_Profiler_Config $provider
     */
    private function __construct(Performance_Main_Provider $provider = null) {
        if ($provider === null) {
            $provider = Performance_Main_Provider::getInstance();
        }

        $this->reset();
        $this->_provider = $provider;
    }

    /**
     * Method for get singleton instance
     *
     * @return Performance_Profiler_Monitor
     */
    public static function getInstance(Performance_Main_Provider $provider = null) {
        if (self::$_instance === false) {
            self::$_instance = new self($provider);
        }

        return self::$_instance;
    }

    /**
     * Reset method.
     *
     * @return Performance_Profiler_Monitor
     */
    public function reset() {
        $this->_storage   = null;
        $this->_isEnabled = false;

        return $this;
    }

    /**
     * Method for enable profiling.
     *
     * @return Performance_Profiler_Monitor
     */
    public function enable() {
        if ($this->_storage === null) {
            $this->_storage = $this->_provider->get('Performance_Profiler_Component_Storage_Factory')->getStorage();
        }

        $startKey = Performance_Profiler_Enum_HttpKeys::PROFILER_START;
        $get = $this->_provider->get('Performance_Main_Web_Component_Request')->getGet();

        if ($get->has($startKey) && strtolower($get->get($startKey)) == 'true') {
            $this->_checkEnable();
            $this->_isEnabled = true;
            $this->_storage->start();
        }

        return $this;
    }

    /**
     * Method to disable profiling.
     *
     * @return Performance_Profiler_Monitor
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
            throw new Performance_Profiler_Exception('Profiler is still enabled.');
        }
    }
}

