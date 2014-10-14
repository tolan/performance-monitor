<?php

namespace PF\Statistic\Engine\Functions;

use PF\Statistic\Engine\Select;

/**
 * This script defines abstract class for create condition for data in statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
abstract class AbstractFunction {

    /**
     * Name of table.
     *
     * @var string
     */
    private $_table;

    /**
     * Alias of column.
     *
     * @var array
     */
    private $_alias;

    /**
     * Value of condition.
     *
     * @var mixed
     */
    private $_value;

    /**
     * Sets name of table.
     *
     * @param string $table Name of table
     *
     * @return \PF\Statistic\Engine\Functions\AbstractFunction
     */
    public function setTable($table) {
        $this->_table = $table;

        return $this;
    }

    /**
     * Sets alias of column.
     *
     * @param string $alias Alias of column
     *
     * @return \PF\Statistic\Engine\Functions\AbstractFunction
     */
    public function setAlias($alias) {
        $this->_alias = $alias;

        return $this;
    }

    /**
     * Sets value of condition.
     *
     * @param mixed $value Value of condition
     *
     * @return \PF\Statistic\Engine\Functions\AbstractFunction
     */
    public function setValue($value) {
        $this->_value = $value;

        return $this;
    }

    /**
     * It adds function condition to statistic select for table, column and alias.
     *
     * @param Select $select Statistic select instance
     * @param string $column Name of column in database table
     *
     * @return Select
     */
    public function addFunction(Select $select, $column) {
        $this->_addFunction($select, $this->_value, $this->_table, $column, $this->_alias);

        return $select;
    }

    /**
     * It adds function condition to statistic select for table, column and alias.
     *
     * @param Select $select Statistic select instance
     * @param mixed  $value  Value of condition
     * @param string $table  Name of table
     * @param string $column Name of column in table
     * @param string $alias  Alias for column
     */
    abstract protected function _addFunction(Select $select, $value, $table, $column, $alias);
}
