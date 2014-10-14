<?php

namespace PF\Statistic\Engine\Data;

use PF\Statistic\Engine;
use PF\Statistic\Enum\Source\Target;

/**
 * This script defines class for assign data condition to statistic select for call entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Call extends AbstractData {

    /**
     * It adds condition for selecting data with time base.
     *
     * @param Engine\Select $select Statistic select instance
     *
     * @return Engine\Select
     */
    public function addTime(Engine\Select $select) {
        $this->createJunction($select, Target::MEASURE);
        $select->columns(array('time' => $select->getTableName(Target::MEASURE).'.started'));

        return $select;
    }

    /**
     * It adds condition for extraction data from content attribute.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     *
     * @return void
     */
    protected function content(Engine\Select $select, Engine\Functions\AbstractFunction $function) {
        $function->setTable(Target::CALL);
        $function->addFunction($select, 'content');
    }

    /**
     * It adds condition for extraction data from file attribute.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     *
     * @return void
     */
    protected function file(Engine\Select $select, Engine\Functions\AbstractFunction $function) {
        $function->setTable(Target::CALL);
        $function->addFunction($select, 'file');
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
        $function->setTable(Target::CALL);
        $function->addFunction($select, 'time');
    }
}
