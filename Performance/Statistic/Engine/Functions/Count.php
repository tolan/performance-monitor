<?php

namespace PM\Statistic\Engine\Functions;

use PM\Statistic\Engine\Select;

/**
 * This script defines class for create condition of count values for data in statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Count extends AbstractFunction {

    /**
     * It adds function condition to statistic select for table, column and alias.
     *
     * @param Select $select Statistic select instance
     * @param mixed  $value  Value of condition
     * @param string $table  Name of table
     * @param string $column Name of column in table
     * @param string $alias  Alias for column
     *
     * @return void
     */
    protected function _addFunction(Select $select, $value, $table, $column, $alias) {
        $cleaned = $select->cleanData('%'.$value.'%');

        $select->columns(array($alias => 'IF('.$select->getTableName($table).'.'.$column.' LIKE '.$cleaned.', 1, 0)'));
    }
}