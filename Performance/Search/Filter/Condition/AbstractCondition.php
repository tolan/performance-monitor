<?php

namespace PM\Search\Filter\Condition;

use PM\Main\Utils;
use PM\Search\Filter\Select;
use PM\Search\Filter\Exception;

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
     * @var \PM\Main\Utils
     */
    private $_utils;

    /**
     * Operator type.
     *
     * @var enum \PM\Search\Enum\Operator
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
     * @param \PM\Main\Utils $utils Utils instance
     */
    public function __construct(Utils $utils) {
        $this->_utils = $utils;
    }

    /**
     * Returns utils instance.
     *
     * @return \PM\Main\Utils
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
     * @param enum  $operator One of \PM\Search\Enum\Operator
     * @param mixed $value    Value for filter condition
     *
     * @return \PM\Search\Filter\Condition\AbstractCondition
     */
    public function prepareFilter($operator, $value) {
        $this->_operator = $operator;
        $this->_value    = $value;

        return $this;
    }

    /**
     * It adds filter to select with table name and column.
     *
     * @param \PM\Search\Filter\Select $select Select instance
     * @param string                   $table  Table name where is column
     * @param string                   $column Column name where will be value
     *
     * @return \PM\Search\Filter\Condition\AbstractCondition
     */
    public function addFilter(Select $select, $table, $column) {
        $method = $this->_utils->toCamelCase($this->_operator);

        $this->$method($select, $this->_value, $table, $column);

        return $this;
    }

    /**
     * Prohibited method.
     *
     * @param \PM\Search\Filter\Select $select Select instance
     *
     * @throws Exception Throws always
     */
    public function fulltext(Select $select) {
        throw new Exception('You can not call fulltext method on non-query condition');
    }
}
