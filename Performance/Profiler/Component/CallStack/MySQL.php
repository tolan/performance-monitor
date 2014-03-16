<?php

namespace PF\Profiler\Component\CallStack;

/**
 * This script defines profiler call stack class with saving tree to MYSQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class MySQL extends AbstractCallStack {

    /**
     * ID of measure attempt
     *
     * @var int
     */
    private $_attemptId;

    /**
     * Repository for loading calls.
     *
     * @var \PF\Profiler\Component\Repository\AttemptData
     */
    private $_repositoryData = null;

    /**
     * Array with calls.
     *
     * @var array
     */
    private $_measuredData = null;

    /**
     * This reset call stack to default values (erase analyzed tree, calls data and attempt information).
     *
     * @return \PF\Profiler\Component\CallStack\MySQL
     */
    public function reset() {
        $this->_attemptId    = null;
        $this->_measuredData = null;
        parent::reset();

        return $this;
    }

    /**
     * Sets ID of attempt.
     *
     * @param int $id ID of attempt
     *
     * @return \PF\Profiler\Component\CallStack\MySQL
     */
    public function setAttemptId($id) {
        $this->_attemptId = $id;

        return $this;
    }

    /**
     * Init method for set repository.
     *
     * @return void
     */
    protected function init() {
        $this->_repositoryData = $this->getProvider()->get('PF\Profiler\Component\Repository\AttemptData');
    }

    /**
     * Returns array with calls.
     *
     * @return array
     */
    protected function getStorageData() {
        if ($this->_measuredData === null) {
            $this->_measuredData = $this->_repositoryData->getDataByAttemptId($this->_attemptId);
        }

        return $this->_measuredData;
    }
}