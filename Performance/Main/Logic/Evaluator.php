<?php

namespace PF\Main\Logic;

/**
 * This script defines class for logic evaluate.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Evaluator {

    /**
     * Analyzed logic tree structure.
     *
     * @var \PF\Main\Logic\Analyze\AbstractElement
     */
    private $_logic;

    /**
     * Scope range.
     *
     * @var mixed
     */
    private $_scope;

    /**
     * Data ranges.
     *
     * @var array
     */
    private $_data = array();

    /**
     * Result of evaluate
     *
     * @var mixed
     */
    private $_result = null;

    /**
     * Performer instance.
     *
     * @var \PF\Main\Logic\Evaluate\AbstractPerformer
     */
    private $_performer = null;

    public function setLogic(Analyze\AbstractElement $logic) {
        $this->_logic  = $logic;
        $this->_result = null;

        return $this;
    }

    /**
     * Returns analyzed logic.
     *
     * @return \PF\Main\Logic\Analyze\AbstractElement
     */
    public function getLogic() {
        return $this->_logic;
    }

    /**
     * Sets scope range. This range restrict result to this scope.
     *
     * @param mixed $scope Scope range (border for result and data)
     *
     * @return \PF\Main\Logic\Evaluator
     */
    public function setScope($scope) {
        $this->_scope  = $scope;
        $this->_result = null;

        return $this;
    }

    /**
     * Returns scope range (border for result and data).
     *
     * @return mixed
     */
    public function getScope() {
        return $this->_scope;
    }

    /**
     * Sets data to storage with defined name.
     *
     * @param string $name Identificator of data
     * @param mixed  $data Data for evaluate
     *
     * @return \PF\Main\Logic\Evaluator
     */
    public function setData($name, $data) {
        $this->_data[$name] = $data;
        $this->_result      = null;

        return $this;
    }

    /**
     * Removes data from storage.
     *
     * @param string $name Identificator of data
     *
     * @return \PF\Main\Logic\Evaluator
     */
    public function removeData($name) {
        if (isset($this->_data[$name])) {
            unset($this->_data[$name]);
            $this->_result = null;
        }

        return $this;
    }

    /**
     * Returns data from storage by defined name.
     *
     * @param string $name Identificator of data
     *
     * @return mixed
     */
    public function getData($name) {
        $data = null;
        if (isset($this->_data[$name])) {
            $data = $this->_data[$name];
        }

        return $data;
    }

    /**
     * Returns performer for evaluate.
     *
     * @return \PF\Main\Logic\Evaluate\AbstractPerformer
     */
    public function getPerformer() {
        if ($this->_performer === null) {
            $this->_performer = new Evaluate\Arrays\Performer();
        }

        return $this->_performer;
    }

    /**
     * Sets performer for evaluate.
     *
     * @param \PF\Main\Logic\Evaluate\AbstractPerformer $performer Performer instance
     *
     * @return \PF\Main\Logic\Evaluator
     */
    public function setPerformer(Evaluate\AbstractPerformer $performer) {
        $this->_performer = $performer;

        return $this;
    }

    /**
     * Returns result of evaluation from logic expression and data in storage (it can be restricted by scope).
     *
     * @param \PF\Main\Logic\Evaluate\AbstractPerformer $performer Performer instance
     *
     * @return mixed
     */
    public function getResult(Evaluate\AbstractPerformer $performer = null) {
        if ($this->_result === null) {
            $performer     = $performer === null ? $this->getPerformer() : $performer;
            $this->_result = $performer->perform($this);
        }

        return $this->_result;
    }
}
