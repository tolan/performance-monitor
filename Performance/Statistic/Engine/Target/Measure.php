<?php

namespace PF\Statistic\Engine\Target;

use PF\Main\Database\Select;
use PF\Statistic\Enum\Source\Target;

/**
 * This script defines class for define measure entity to statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Measure extends AbstractTarget {

    /**
     * Sets measure entity to statistic select.
     *
     * @param Select $select Statistic select instance
     *
     * @return Measure
     */
    public function setTarget(Select $select) {
        $select->from(array($select->getTableName(Target::MEASURE) => 'test_measure'), array());

        return $this;
    }
}
