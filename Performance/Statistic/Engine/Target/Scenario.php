<?php

namespace PM\Statistic\Engine\Target;

use PM\Main\Database\Select;
use PM\Statistic\Enum\Source\Target;

/**
 * This script defines class for define scenario entity to statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Scenario extends AbstractTarget {

    /**
     * Sets scenario entity to statistic select.
     *
     * @param Select $select Statistic select instance
     *
     * @return Scenario
     */
    public function setTarget(Select $select) {
        $select->from(array($select->getTableName(Target::SCENARIO) => 'scenario'), array());

        return $this;
    }
}