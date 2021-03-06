<?php

namespace PM\Profiler\Monitor;

use PM\Profiler\Entity;
use PM\Profiler\Monitor\Filter\Enum;

/**
 * This script defines class for monitor ticker. This class catch each call and process it.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Ticker implements Interfaces\Ticker {

    /**
     * Monitor storage instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Storage
     */
    private $_storage;

    /**
     * List of filters.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Filter[]
     */
    private $_filters = array();

    /**
     * Flag that ticker is running.
     *
     * @var bool
     */
    private $_isRunning = false;

    /**
     * Start time of last call or start of measure.
     *
     * @var float
     */
    private $_startTime = 0;

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
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
     * @param \PM\Profiler\Monitor\Interfaces\Filter $filter Filter instance
     *
     * @return \PM\Profiler\Monitor\Ticker
     */
    public function addFilter(Interfaces\Filter $filter) {
        $this->_filters[] = $filter;

        return $this;
    }

    /**
     * Starts tick function.
     *
     * @return \PM\Profiler\Monitor\Ticker
     */
    public function start() {
        if ($this->_isRunning === false) {
            $this->_storage->setState(Storage\State::STATE_TICKING);
            $this->_isRunning = true;
            $this->_startTime = $this->_getMicrotime();
            $this->_registerTick();
        }

        return $this;
    }

    /**
     * Stops tick function.
     *
     * @return \PM\Profiler\Monitor\Ticker
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
     * Returns wheter run ticker function.
     *
     * @return boolean
     */
    public function isRuning() {
        return $this->_isRunning;
    }

    /**
     * Tick function for catch each function calling.
     *
     * @return void
     */
    public function tick() {
        $endTime   = $this->_getMicrotime();
        $backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if ($this->_isAllowedBacktrace($backtrace)) {
            $call = $this->_storage->getCallInstance()->createCall($backtrace, $this->_startTime, $endTime);
            $this->_storage->offsetSet(null, $call);
        }

        $this->_startTime = $this->_getMicrotime();
    }

    /**
     * It evaluate that backtrace is allowed for catching.
     *
     * @param array $backtrace Backtrace debug
     *
     * @return boolean
     */
    private function _isAllowedBacktrace($backtrace) {
        $isAllow = true;
        reset($this->_filters);

        do {
            $filter  = current($this->_filters); /* @var $filter Filter */
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
        return microtime(true) * 1000;
    }

    /**
     * Register tick function and init default attributes.
     *
     * @return \PM\Profiler\Monitor\Ticker
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
     * @return \PM\Profiler\Monitor\Ticker
     */
    private function _unregisterTick() {
        unregister_tick_function(array(&$this, 'tick'));

        return $this;
    }

    /**
     * Loads default filter. It is for avoid catch calls in PM.
     *
     * @return \PM\Profiler\Monitor\Filter
     */
    private function _getDefaultFilter() {
        // TODO move to another place
        $filter = array(
            'type' => Enum\Type::NEGATIVE,
            'parameters' => array(
                array(
                    'operator'  => Enum\Operator::REG_EXP,
                    'value'     => dirname(dirname(__DIR__)),
                    'parameter' => Enum\Parameter::FILE
                )
            )
        );

        return new Filter(
            new Entity\Filter($filter)
        );
    }
}
