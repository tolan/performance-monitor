<?php

namespace PF\Statistic\Engine\Wrapper;

/**
 * This script defines class for wrapping statistic select with line base.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Line extends AbstractWrapper {

    /**
     * Adds column which will be group for line base.
     *
     * @param string $column Column name of selected data
     *
     * @return Line
     */
    public function addColumn($column) {
        $this->getSelect()->columns(array($column => 'SUM('.$column.')'));

        return $this;
    }
}
