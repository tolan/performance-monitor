<?php

namespace PF\Search\Filter\Condition;

use PF\Search\Filter\Select;
use PF\Search\Filter\Exception;

/**
 * This script defines abstract class for condition. Each condition is injected to junction where is set table name and column.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
abstract class AbstractCondition {

    /**
     * Utils instance.
     *
     * @var \Performance_Main_Utils
     */
    private $_utils;

    /**
     * Operator type.
     *
     * @var enum \PF\Search\Enum\Operator
     */
    private $_operator;

    /**
     * Value for filter condition.
     *
     * @var mixed
     */
    private $_value;

    /**
     * Construct method.
     *
     * @param \Performance_Main_Utils $utils Utils instance
     */
    public function __construct(\Performance_Main_Utils $utils) {
        $this->_utils = $utils;
    }

    /**
     * Returns utils instance.
     *
     * @return \Performance_Main_Utils
     */
    protected function getUtils() {
        return $this->_utils;
    }

    /**
     * Returns value for condition.
     *
     * @return mixed
     */
    protected function getValue() {
        return $this->_value;
    }

    /**
     * It prepare operator and value for filter condition.
     *
     * @param enum  $operator One of \PF\Search\Enum\Operator
     * @param mixed $value    Value for filter condition
     *
     * @return \PF\Search\Filter\Condition\AbstractCondition
     */
    public function prepareFilter($operator, $value) {
        $this->_operator = $operator;
        $this->_value    = $value;

        return $this;
    }

    /**
     * It adds filter to select with table name and column.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param string                   $table  Table name where is column
     * @param string                   $column Column name where will be value
     *
     * @return \PF\Search\Filter\Condition\AbstractCondition
     */
    public function addFilter(Select $select, $table, $column) {
        $method = $this->_utils->toCamelCase($this->_operator);

        $this->$method($select, $this->_value, $table, $column);

        return $this;
    }

    public function fulltext(Select $select) {
        throw new Exception('You can not call fulltext method on non-query condition');
    }
}
