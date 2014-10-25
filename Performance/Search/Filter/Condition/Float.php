<?php

namespace PM\Search\Filter\Condition;

use PM\Search\Filter\Select;

/**
 * This script defines class for filter condition with float type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Float extends AbstractCondition {

    /**
     * It adds filter to select with "greater than" condidtion.
     *
     * @param \PM\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function greaterThan(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' > ?', $value);
    }

    /**
     * It adds filter to select with "less than" condidtion.
     *
     * @param \PM\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function lessThan(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' < ?', $value);
    }
}
