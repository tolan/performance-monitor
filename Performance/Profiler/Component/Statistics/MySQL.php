<?php

namespace PF\Profiler\Component\Statistics;

use PF\Profiler\Enum\CallParameters;

/**
 * This script defines profiler statistic class for access to MYSQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class MySQL extends AbstractStatistics {

    /**
     * ID of attempt.
     *
     * @var int
     */
    private $_attemptId;

    /**
     * Repository for measure statistic.
     *
     * @var \PF\Profiler\Component\Repository\TestAttempt
     */
    private $_attemptRepository     = null;

    /**
     * Repository for measure statistic data.
     *
     * @var \PF\Profiler\Component\Repository\AttemptStatisticData
     */
    private $_statDataRepository = null;

    /**
     * Analyzed call stack tree.
     *
     * @var array
     */
    private $_callStack = null;

    /**
     * Counter for calls.
     *
     * @var int
     */
    private $_countCalls = 0;
    
    private $_times = array();

    /**
     * Method for reset variable to defaults (reset atempt ID, statistics, file cache, times, count of calls).
     *
     * @return void
     */
    public function reset() {
        $this->_attemptId  = null;
        $this->_countCalls = 0;
        $this->_times      = array();
        
        return parent::reset();
    }

    /**
     * Sets ID of attempt.
     *
     * @param int $id ID of attempt
     *
     * @return \PF\Profiler\Component\Statistics\MySQL
     */
    public function setAttemptId($id) {
        $this->_attemptId = $id;
        $attempt          = $this->_attemptRepository->getAttempt($id);
        $this->setCompensationTime($attempt['compensationTime']);

        return $this;
    }
    
    /**
     * Save statistics to MYSQL.
     *
     * @return \PF\Profiler\Component\Statistics\MySQL
     */
    public function save() {
        $this->generate();
        $this->_times = $this->getTimes();
        $this->_save($this->getStatistics(), $this->_attemptId);
        $updateData = array(
            'time'  => $this->_times[0],
            'calls' => $this->_countCalls
        );
        $this->_attemptRepository->update($this->_attemptId, $updateData);

        return $this;
    }

    protected function getAnalyzedTree() {
        return $this->_callStack->getAnalyzedTree();
    }

    /**
     * Init method for set call stack and repositories.
     *
     * @return void
     */
    protected function init() {
        $this->_callStack          = $this->getProvider()->get('PF\Profiler\Component\CallStack\Factory')->getCallStack();
        $this->_attemptRepository  = $this->getProvider()->get('PF\Profiler\Component\Repository\TestAttempt');
        $this->_statDataRepository = $this->getProvider()->get('PF\Profiler\Component\Repository\AttemptStatisticData');
    }

    /**
     * It saves statistics data to MYSQL.
     *
     * @param array $statistics Analyzed trre with statistics
     * @param int   $attemptId  ID of attempt
     * @param int   $parent     ID of statistic data parent id (0 is root)
     *
     * @return \PF\Profiler\Component\Statistics\MySQL
     */
    private function _save(&$statistics, $attemptId = null, $parent = 0) {
        $this->_times[$parent] = 0;
        $respository = $this->_statDataRepository;

        foreach ($statistics as &$call) {
            $this->_times[$parent] += $call['timeSubStack'];
            $this->_countCalls++;
            $data = array(
                'attemptId'    => $attemptId,
                'parentId'     => $parent,
                'file'         => $call[CallParameters::FILE],
                'line'         => $call[CallParameters::LINE],
                'content'      => $call[CallParameters::CONTENT],
                'time'         => $call[CallParameters::TIME],
                'timeSubStack' => $call[CallParameters::TIME_SUB_STACK]
            );
            $id = $respository->create($data);
            if (isset($call[CallParameters::SUB_STACK])) {
                $this->_save($call[CallParameters::SUB_STACK], $attemptId, $id);
            }
        }

        return $this;
    }
}