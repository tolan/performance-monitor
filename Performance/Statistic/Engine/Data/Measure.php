<?php

namespace PF\Statistic\Engine\Data;

use PF\Statistic\Engine;
use PF\Statistic\Enum\Source\Target;

/**
 * This script defines class for assign data condition to statistic select for measure entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Measure extends AbstractData {

    /**
     * It adds condition for selecting data with time base.
     *
     * @param Engine\Select $select Statistic select instance
     *
     * @return Engine\Select
     */
    public function addTime(Engine\Select $select) {
        $select->columns(array('time' => $select->getTableName(Target::MEASURE).'.started'));
    }

    /**
     * It adds condition for extraction data from calls attribute.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     *
     * @return void
     */
    protected function calls(Engine\Select $select, Engine\Functions\AbstractFunction $function) {
        $function->setTable(Target::MEASURE);
        $function->addFunction($select, 'calls');
    }

    /**
     * It adds condition for extraction data from method attribute.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     *
     * @return void
     */
    protected function method(Engine\Select $select, Engine\Functions\AbstractFunction $function) {
        $function->setTable(Target::MEASURE);
        $function->addFunction($select, 'method');
    }

    /**
     * It adds condition for extraction data from time attribute.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     *
     * @return void
     */
    protected function time(Engine\Select $select, Engine\Functions\AbstractFunction $function) {
        $function->setTable(Target::MEASURE);
        $function->addFunction($select, 'time');
    }

    /**
     * It adds condition for extraction data from url attribute.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     *
     * @return void
     */
    protected function url(Engine\Select $select, Engine\Functions\AbstractFunction $function) {
        $function->setTable(Target::MEASURE);
        $function->addFunction($select, 'url');
    }
}
