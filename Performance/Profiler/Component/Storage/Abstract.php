<?php

/**
 * Abstract class for profiler storage class which save each call and register tick function.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class Performance_Profiler_Component_Storage_Abstract {

    /**
     * Pointer to next call in storage.
     *
     * @var int
     */
    private $_pointer   = 0;

    /**
     * Storage for calls.
     *
     * @var array
     */
    private $_storage   = array();

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
    }

    /**
     * Method for begin measure. It register tick and shutdown function.
     *
     * @return Performance_Profiler_Component_Storage_Abstract
     *
     * @throws Performance_Profiler_Exception Throws when it doesn't user function 'debug_backtrace'.
     */
    final public function start() {
        if (is_callable('debug_backtrace') === false) {
            throw new Performance_Profiler_Exception('Storage requires function debug_backtrace.');
        }

        register_tick_function(array(&$this, 'tick'));
        register_shutdown_function(array(&$this, 'shutdownTick'));
        $this->_storage[$this->_pointer]['startTime'] = $this->_getMicrotime();

        return $this;
    }

    /**
     * Method for stop measure. It unregister tick function.
     *
     * @return Performance_Profiler_Component_Storage_Abstract
     */
    final public function stop() {
        unregister_tick_function(array(&$this, 'tick'));
        $this->shutdownTick();

        return $this;
    }

    /**
     * Tick method that is called when calling any function. Excluded all from performance scope.
     *
     * @return void
     */
    final public function tick() {
        $time = $this->_getMicrotime();
        $bt   = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if(!$this->isAllowedTrace($bt)) {
            return false;
        }

        $actual = array(
            'file' => $bt[0]['file'],
            'line' => $bt[0]['line'],
            'immersion' => count($bt)
        );

        $this->_storage[$this->_pointer]['stack']   = $actual;
        $this->_storage[$this->_pointer]['endTime'] = $time;

        $this->_pointer++;

        if ($this->_pointer > 0) {
            $this->_storage[$this->_pointer]['startTime'] = $time;
        }
    }

    /**
     * This method resolve that backtrace is allowed to save.
     *
     * @param array $bt Backtrace
     *
     * @return boolean
     */
    final protected function isAllowedTrace(&$bt) {
        if (strstr($bt[0]['file'], '/Performance/Profiler') || strstr($bt[0]['file'], '/Performance/Main')) {
            return false;
        }

        return true;
    }

    /**
     * This method is called at the end of measure. It clean storage.
     *
     * @return void
     */
    final public function shutdownTick() {
        $lastKey = end(array_keys($this->_storage));
        if (!isset($this->_storage[$lastKey]['endTime'])) {
            unset($this->_storage[$lastKey]);
        }
    }

    /**
     * Method save must implement all children. It is for save to global storage (cache, mysql, etc.).
     */
    abstract public function save();

    /**
     * Gets storage of all calls.
     *
     * @return array
     */
    public function getStorageCalls() {
        return $this->_storage;
    }

    /**
     * Gets count of all registred calls.
     *
     * @return int
     */
    public function getCallsCount() {
        return $this->_pointer;
    }

    /**
     * Gets time in microsecond in requested format.
     *
     * @return float
     */
    private function _getMicrotime() {
        return microtime(true);
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
