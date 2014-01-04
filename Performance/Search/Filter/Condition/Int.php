<?php

namespace PF\Search\Filter\Condition;

use PF\Search\Filter\Select;

/**
 * This script defines class for filter condition with integer type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Int extends Float {

    /**
     * It adds filter to select with "equal" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function equal(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' = ?', $value);
    }

    /**
     * It adds filter to select with "not equal" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function notEqual(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' != ?', $value);
    }
}
