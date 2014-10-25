<?php

namespace PM\Profiler\Monitor;

/**
 * This script defines class for monitor statistic.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Statistic implements Interfaces\Statistic {

    /**
     * Monitor storage instance
     *
     * @var \PM\Profiler\Monitor\Interfaces\Storage
     */
    private $_storage;

    /**
     * This time is for compensation of each call, because call ticking take some time.
     *
     * @var int
     */
    private $_compensationTime = 0;

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     *
     * @return void
     */
    public function __construct(Interfaces\Storage $storage) {
        $this->_storage = $storage;
    }

    /**
     * Generate statistics information in call stack tree.
     *
     * @return \PM\Profiler\Monitor\Statistic
     */
    public function generate() {
        if ($this->_storage->getState() !== Storage\State::STATE_STAT_GENERATED) {
            $this->_storage->setState(Storage\State::STATE_STAT_GENERATING);
            $this->_computeCompensationTime();
            $this->_storage->rewind();
            $processed = $this->_generate($this->_storage);
            $this->_storage->fromArray($processed);
            $this->_storage->setState(Storage\State::STATE_STAT_GENERATED);
        }

        return $this;
    }

    /**
     * Generate statistics information in call stack tree.
     *
     * @param \PM\Profiler\Monitor\Storage $storage Monitor storage instance
     *
     * @return array
     */
    private function _generate(Storage $storage) {
        $result = array();
        while($storage->valid()) {
            $call = $storage->arrayShift();

            $this->_generateCall($call);

            $result[] = $call;
        }

        return $result;
    }

    /**
     * Generate statistics for call and save it into call in parameter.
     *
     * @param array $call Information about call
     *
     * @return void
     */
    private function _generateCall(&$call) {
        $call[Enum\CallAttributes::TIME] = $this->_getTime($call);

        if (isset($call[Enum\CallAttributes::SUB_STACK])) {
            $this->_generateSubTree($call[Enum\CallAttributes::SUB_STACK]);
            $time = $this->_getTimeSubStack($call[Enum\CallAttributes::SUB_STACK]);
            $call[Enum\CallAttributes::TIME_SUB_STACK] = $call[Enum\CallAttributes::TIME] + $time;
        }
    }

    /**
     * Generate statistics for list of calls and save it into list in parameter.
     *
     * @param array $tree List of calls
     *
     *
     * @return void
     */
    private function _generateSubTree(&$tree) {
        foreach ($tree as &$call) {
            $this->_generateCall($call);
        }
    }

    /**
     * It compute new time of sub-stack.
     *
     * @param array $subStack Array with calls in sub-stack
     *
     * @return float
     */
    private function _getTimeSubStack(&$subStack) {
        $time = 0;
        foreach($subStack as &$subCall) {
            if (isset($subCall[Enum\CallAttributes::TIME_SUB_STACK])) {
                $time += $subCall[Enum\CallAttributes::TIME_SUB_STACK];
            } else {
                $time += $subCall[Enum\CallAttributes::TIME];
            }
        }

        return $time;
    }

    /**
     * Return compensated time for call.
     *
     * @param array $call Array with call
     *
     * @return float
     */
    private function _getTime(&$call) {
        $start = isset($call[Enum\CallAttributes::START_TIME]) ? $call[Enum\CallAttributes::START_TIME] : 0;
        $end   = isset($call[Enum\CallAttributes::END_TIME])   ? $call[Enum\CallAttributes::END_TIME]   : 0;

        return max((($end - $start) - $this->_compensationTime), 0);
    }

    /**
     * Method for computing compensation time. It is for compensate time consumed by profiler.
     *
     * @return void
     */
    private function _computeCompensationTime() {
        $start = microtime(true);

        for($i = 0; $i < 100; $i++) {
            $this->_getMicrotime();
        }

        $end = microtime(true);

        $this->_compensationTime = ($end - $start) / 100;
    }

    /**
     * Returns microtime. It is for compute compensate time.
     *
     * @return float
     */
    private function _getMicrotime() {
        return microtime(true) * 1000;
    }
}
