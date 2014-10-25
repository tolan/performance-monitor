<?php

namespace PM\Search\Filter\Condition;

use PM\Search\Filter\Select;

/**
 * This script defines class for filter condition with string type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class String extends AbstractCondition {

    /**
     * It adds filter to select with "equal" condidtion.
     *
     * @param \PM\Search\Filter\Select $select Select instance
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
     * It adds filter to select with "contains" condidtion.
     *
     * @param \PM\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function contains(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' LIKE ?', '%'.$value.'%');

    }

    /**
     * It adds filter to select with "does not contains" condidtion.
     *
     * @param \PM\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function doesNotContains(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' NOT LIKE ?', '%'.$value.'%');
    }
}
