<?php

namespace PF\Profiler\Component\Storage;

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
        $startTime  = $calls[0]['startTime'];

        foreach ($calls as $call) {
            $repository->create(
                array(
                    'attemptId' => $id,
                    'file'      => $call['stack']['file'],
                    'line'      => $call['stack']['line'],
                    'immersion' => $call['stack']['immersion'],
                    'start'     => ($call['startTime'] - $startTime) * 1000,
                    'end'       => ($call['endTime'] - $startTime) * 1000
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
            'file' => $bt[0]['file'],
            'line' => $bt[0]['line'],
            'immersion' => count($bt)
        );

        $this->_compStack[$this->_compRuns]['stack']   = $actual;
        $this->_compStack[$this->_compRuns]['endTime'] = $time;

        $this->_compRuns++;

        if ($this->_compRuns > 0) {
            $this->_compStack[$this->_compRuns]['startTime'] = $time;
        }
    }
}
