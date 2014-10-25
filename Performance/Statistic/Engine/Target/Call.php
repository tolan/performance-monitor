<?php

namespace PM\Statistic\Engine\Target;

use PM\Main\Database\Select;
use PM\Statistic\Enum\Source\Target;

/**
 * This script defines class for define call entity to statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Call extends AbstractTarget {

    /**
     * Sets call entity to statistic select.
     *
     * @param Select $select Statistic select instance
     *
     * @return Call
     */
    public function setTarget(Select $select) {
        $select->from(array($select->getTableName(Target::CALL) => 'measure_statistic_data'), array());

        return $this;
    }
}