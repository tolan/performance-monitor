<?php

namespace PF\Search\Filter\Condition;

use PF\Search\Filter\Select;

/**
 * This script defines class for filter condition with date type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Date extends AbstractCondition {

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
        $time  = floor(strtotime($value)/60)*60;
        $start = $this->getUtils()->convertTimeToMySQLDateTime($time);
        $end   = $this->getUtils()->convertTimeToMySQLDateTime($time+59);

        $this->after($select, $start, $table, $column);
        $this->before($select, $end, $table, $column);
    }

    /**
     * It adds filter to select with "after" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function after(Select $select, $value, $table, $column) {
        $time  = floor(strtotime($value)/60)*60;
        $start = $this->getUtils()->convertTimeToMySQLDateTime($time);
        $select->where($table.'.'.$column.' >= ?', $start);

    }

    /**
     * It adds filter to select with "before" condidtion.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param mixed                    $value  Value for search
     * @param string                   $table  Table name where is column
     * @param string                   $column Column where is value
     *
     * @return void
     */
    protected function before(Select $select, $value, $table, $column) {
        $time = floor(strtotime($value)/60)*60;
        $end  = $this->getUtils()->convertTimeToMySQLDateTime($time);
        $select->where($table.'.'.$column.' <= ?', $end);
    }
}
