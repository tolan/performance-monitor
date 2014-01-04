<?php

namespace PF\Search\Filter\Condition;

use PF\Search\Filter\Select;

/**
 * This script defines class for filter condition with enum type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Enum extends AbstractCondition {

    /**
     * It adds filter to select with "in" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function in (Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' IN (?)', $value);
    }

    /**
     * It adds filter to select with "not in" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function notIn(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' NOT IN (?)', $value);
    }

    /**
     * It adds filter to select with "is set" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function set(Select $select, $value, $table, $column) {
        $select->where($table.'.'.$column.' IS NOT NULL AND '.$table.'.'.$column.' != \'\'');
    }
}
