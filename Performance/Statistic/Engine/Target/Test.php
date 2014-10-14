<?php

namespace PF\Statistic\Engine\Target;

use PF\Main\Database\Select;
use PF\Statistic\Enum\Source\Target;

/**
 * This script defines class for define test entity to statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Test extends AbstractTarget {

    /**
     * Sets test entity to statistic select.
     *
     * @param Select $select Statistic select instance
     *
     * @return Test
     */
    public function setTarget(Select $select) {
        $select->from(array($select->getTableName(Target::TEST) => 'scenario_test'), array());

        return $this;
    }
}