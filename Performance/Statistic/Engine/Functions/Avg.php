<?php

namespace PM\Statistic\Engine\Functions;

use PM\Statistic\Engine\Select;
use PM\Statistic\Engine\Exception;

/**
 * This script defines class for create condition of average value for data in statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Avg extends AbstractFunction {

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
        if (!empty($value['from']) && !empty($value['to'])) {
            $from     = $select->cleanData($value['from']);
            $to       = $select->cleanData($value['to']);
            $function = 'AVG('.$select->getTableName($table).'.'.$column.') BETWEEN '.$from.' AND '.$to;
        } elseif(!empty($value['from'])) {
            $from     = $select->cleanData($value['from']);
            $function = 'AVG('.$select->getTableName($table).'.'.$column.') >= '.$from;
        } elseif(!empty($value['to'])) {
            $from     = $select->cleanData($value['from']);
            $function = 'AVG('.$select->getTableName($table).'.'.$column.') <= '.$from;
        } else {
            throw new Exception('Invalid value.');
        }

        $select->columns(array($alias => $function));
    }
}
