<?php

namespace PM\Statistic\Engine\Data;

use PM\Statistic\Engine;
use PM\Statistic\Enum\Source\Target;

/**
 * This script defines class for assign data condition to statistic select for scenario entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Scenario extends AbstractData {

    /**
     * It adds condition for selecting data with time base.
     *
     * @param Engine\Select $select Statistic select instance
     *
     * @return Engine\Select
     */
    public function addTime(Engine\Select $select) {
        $this->createJunction($select, Target::TEST);
        $select->columns(array('time' => $select->getTableName(Target::TEST).'.started'));
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
        $this->createJunction($select, Target::MEASURE);
        $function->setTable(Target::MEASURE);
        $function->addFunction($select, 'method');
    }
}
