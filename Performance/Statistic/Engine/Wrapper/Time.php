<?php

namespace PF\Statistic\Engine\Wrapper;

use PF\Statistic\Engine\Select;

/**
 * This script defines class for wrapping statistic select with time base.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Time extends AbstractWrapper {

    /**
     * Adds column which will be group for time base.
     *
     * @param string $column Column name of selected data
     *
     * @return Line
     */
    public function addColumn($column) {
        $this->getSelect()->columns(array($column => 'SUM('.$column.')'));
    }

    /**
     * It compiles wrapper select for time base.
     *
     * @return Line
     */
    protected function compile() {
        $wrapper = $this->getWrapper(); /* @var $wrapper Select */

        $wrapper->columns(array('time' => self::TABLE_NAME.'.time'));
        $wrapper->group(array('ROUND('.self::TABLE_NAME.'.time, 3)'));

        parent::compile();

        return $this;
    }
}
