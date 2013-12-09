<?php

/**
 * This script defines profiler call stack class with saving tree to MYSQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_CallStack_MySQL extends Performance_Profiler_Component_CallStack_Abstract {

    /**
     * ID of measure attempt
     *
     * @var int
     */
    private $_attemptId;

    /**
     * Repository for loading calls.
     *
     * @var Performance_Profiler_Component_Repository_MeasureData
     */
    private $_repositoryData = null;

    /**
     * Array with calls.
     *
     * @var array
     */
    private $_measuredData = null;

    /**
     * Indicator for immersion level.
     *
     * @var int
     */
    private $_actualLevel = 1;

    /**
     * Array with analyzed tree.
     *
     * @var array
     */
    private $_analyzedTree = array();

    /**
     * This reset call stack to default values (erase analyzed tree, calls data and attempt information).
     *
     * @return Performance_Profiler_Component_CallStack_MySQL
     */
    public function reset() {
        $this->_attemptId    = null;
        $this->_measuredData = null;
        $this->_actualLevel  = 1;
        $this->_analyzedTree = array();

        return $this;
    }

    /**
     * Sets ID of attempt.
     *
     * @param int $id ID of attempt
     *
     * @return Performance_Profiler_Component_CallStack_MySQL
     */
    public function setAttemptId($id) {
        $this->_attemptId = $id;

        return $this;
    }

    /**
     * This create analyzed tree from calls.
     *
     * @return Performance_Profiler_Component_CallStack_MySQL
     */
    public function analyze() {
        if (empty($this->_analyzedTree)) {
            $data = $this->_getData();
            $this->_analyzedTree = $this->_analyzeTree($data);
        }

        return $this;
    }

    /**
     * Returns analyzed tree.
     *
     * @return array Array with analyzed call stack tree
     */
    public function getAnalyzedTree() {
        $this->analyze();

        return $this->_analyzedTree;
    }

    /**
     * Init method for set repository.
     *
     * @return void
     */
    protected function init() {
        $this->_repositoryData = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureData');
    }

    /**
     * This analyze call stack tree from calls.
     *
     * @param array $stack Array with calls
     *
     * @return array
     */
    private function _analyzeTree(&$stack) {
        $result = array();

        while(!empty($stack)) {
            $call = array_shift($stack);

            if ($call['immersion'] == $this->_actualLevel) {
                $result[] = $call;
            } elseif ($call['immersion'] > $this->_actualLevel) {
                $this->_actualLevel++;
                array_unshift($stack, $call);
                $tmp = $this->_analyzeTree($stack);
                $result[] = $tmp;
            } else {
                $this->_actualLevel--;
                $call['subStack'] = $result;
                return $call;
            }
        }

        return $result;
    }

    /**
     * Returns array with calls.
     *
     * @return array
     */
    private function _getData() {
        if ($this->_measuredData === null) {
            $this->_measuredData =$this->_repositoryData->getDataByAttemptId($this->_attemptId);
        }

        return $this->_measuredData;
    }
}