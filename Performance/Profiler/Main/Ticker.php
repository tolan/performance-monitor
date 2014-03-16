<?php

namespace PF\Profiler\Main;

class Ticker implements Interfaces\Ticker {

    /**
     *
     * Storage instance.
     *
     * @var \PF\Profiler\Main\Interfaces\Storage
     */
    private $_storage;

    /**
     * List of filters.
     *
     * @var \PF\Profiler\Main\Interfaces\Filter[]
     */
    private $_filters = array();

    /**
     * Flag that ticker is running.
     *
     * @var bool
     */
    private $_isRunning = false;

    /**
     * Start time of actual call.
     *
     * @var float
     */
    private $_startTime = 0;

    /**
     * Construct method.
     *
     * @param \PF\Profiler\Main\Interfaces\Storage $storage Storage instance
     *
     * @return void
     */
    public function __construct(Interfaces\Storage $storage) {
        $this->_storage = $storage;

        $this->addFilter($this->_getDefaultFilter());
    }

    /**
     * Adds filter to stack for filtering calls backtrace.
     *
     * @param \PF\Profiler\Main\Interfaces\Filter $filter Filter instance
     *
     * @return \PF\Profiler\Main\Ticker
     */
    public function addFilter(Interfaces\Filter $filter) {
        $this->_filters[] = $filter;

        return $this;
    }

    /**
     * Starts tick function.
     *
     * @return \PF\Profiler\Main\Ticker
     */
    public function start() {
        if ($this->_isRunning === false) {
            $this->_storage->setState(Storage\State::STATE_TICKING);
            $this->_isRunning = true;
            $this->_registerTick();
            $this->_startTime = $this->_getMicrotime();
        }

        return $this;
    }

    /**
     * Stops tick function.
     *
     * @return \PF\Profiler\Main\Ticker
     */
    public function stop() {
        if ($this->_isRunning === true) {
            $this->_storage->setState(Storage\State::STATE_TICKED);
            $this->_unregisterTick();
            $this->_isRunning = false;
        }

        return $this;
    }

    /**
     * Return flag that ticker function run.
     *
     * @return boolean
     */
    public function isRuning() {
        return $this->_isRunning;
    }

    /**
     * Tick function for each function calling.
     *
     * @return void
     */
    public function tick() {
        $endTime   = $this->_getMicrotime();
        $backtrace = debug_backtrace();

        if ($this->_isAllowedBacktrace($backtrace)) {
            $call = $this->_storage->getCallInstance()->createCall($backtrace, $this->_startTime, $endTime);
            $this->_storage->offsetSet(null, $call);
        }

        $this->_startTime = $this->_getMicrotime();
    }

    private function _isAllowedBacktrace($backtrace) {
        $isAllow = true;
        reset($this->_filters);

        do {
            $filter  = current($this->_filters);
            $isAllow = $filter->isAllowedBacktrace($backtrace);
        } while($isAllow && next($this->_filters));

        return $isAllow;
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
     * Register tick function and init default attributes.
     *
     * @return \PF\Profiler\Main\Ticker
     */
    private function _registerTick() {
        if (is_callable('debug_backtrace') === false) {
            throw new Exception('Ticker requires function debug_backtrace.');
        }

        register_tick_function(array(&$this, 'tick'));

        return $this;
    }

    /**
     * Unregister tick function and finish process default attributes.
     *
     * @return \PF\Profiler\Main\Ticker
     */
    private function _unregisterTick() {
        unregister_tick_function(array(&$this, 'tick'));

        return $this;
    }

    private function _getDefaultFilter() {
        // TODO move to another place
        return new Filter(array());
    }
}
