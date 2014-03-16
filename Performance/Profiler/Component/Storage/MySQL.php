<?php

namespace PF\Profiler\Component\Storage;

use PF\Profiler\Enum\CallParameters;

/**
 * This script defines profiler storage class for access to MYSQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class MySQL extends AbstractStorage {

    /**
     * ID of attempt.
     *
     * @var int
     */
    private $_attemptId = null;

    /**
     * Count of compensation runs. It is for compensation when each function is ticked.
     *
     * @var int
     */
    private $_compRuns = 0;

    /**
     * Compensation stack, it is for compensation when each function is ticked.
     *
     * @var int
     */
    private $_compStack = array();

    /**
     * Sets ID of attempt.
     *
     * @param int $id ID of attempt
     *
     * @return \PF\Profiler\Component\Storage\MySQL
     */
    public function setAttemptId($id) {
        $this->_attemptId = $id;

        return $this;
    }

    /**
     * It saves all calls and update attempt with computed compensation time.
     *
     * @return void
     */
    public function save() {
        $id         = $this->_attemptId;
        $repository = $this->getProvider()
            ->get('PF\Profiler\Component\Repository\AttemptData'); /* @var $repository \PF\Profiler\Component\Repository\AttemptData */
        $calls      = &$this->getStorageCalls();
        $startTime  = $calls[0][CallParameters::END_TIME];

        foreach ($calls as $call) {
            $repository->create(
                array(
                    'attemptId' => $id,
                    'file'      => $call[CallParameters::FILE],
                    'line'      => $call[CallParameters::LINE],
                    'immersion' => $call[CallParameters::IMMERSION],
                    'start'     => ($call[CallParameters::START_TIME] - $startTime) * 1000,
                    'end'       => ($call[CallParameters::END_TIME] - $startTime) * 1000
                )
            );
        }

        $compTime          = $this->_compensationTime();
        $repositoryAttempt = $this->getProvider()
            ->get('PF\Profiler\Component\Repository\TestAttempt'); /* @var $repositoryAttempt \PF\Profiler\Component\Repository\TestAttempt */
        $repositoryAttempt->update(
            $id,
            array(
                'compensationTime' => $compTime * 1000,
                'started'          => $startTime
            )
        );
    }

    /**
     * It calculates average time for compensation of each call.
     *
     * @return float
     */
    private function _compensationTime() {
        $count = 100;

        register_tick_function(array(&$this, '_compensationEmptyFunction'));
        declare(ticks=1);

        $start = microtime(true);
        for($i = 0; $i < $count; $i++) {
        }
        $end = (microtime(true) - $start) / ($this->_compRuns+2);
        unregister_tick_function(array(&$this, '_compensationEmptyFunction'));

        return $end;
    }

    /**
     * This method mimics the behavior of a standard function for tick.
     *
     * @return void
     */
    private function _compensationEmptyFunction() {
        $time = microtime(true);
        $bt   = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        if(!$this->isAllowedTrace($bt)) {
        }

        $actual = array(
            CallParameters::FILE => $bt[0][CallParameters::FILE],
            CallParameters::LINE => $bt[0][CallParameters::LINE],
            CallParameters::IMMERSION => count($bt),
            CallParameters::START_TIME => $this->_compStack[$this->_compRuns][CallParameters::START_TIME],
            CallParameters::END_TIME => $time
        );

        $this->_compStack[$this->_compRuns]   = $actual;

        $this->_compRuns++;

        if ($this->_compRuns > 0) {
            $this->_compStack[$this->_compRuns][CallParameters::START_TIME] = $time;
        }
    }
}
