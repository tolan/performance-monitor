<?php

namespace PF\Statistic\Engine\Junction;

use PF\Main\Database;
use PF\Statistic\Engine\Select;

/**
 * This script defines abstract class for assign junction to target entity in statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
abstract class AbstractJunction {

    /**
     * Table alias for source entity
     */
    const SOURCE_TABLE = 'source';

    /**
     * Abstract method for create junction to target entity.
     *
     * @param Select $select Statistic select instance
     * @param string $target One of enum \PF\Statistic\Enum\Source\Target
     */
    abstract public function createJunction(Select $select, $target);

    /**
     * Abstract method for assing source select to statistic select.
     *
     * @param Select          $targetSelect Statistic select instance
     * @param Database\Select $sourceSelect Source select instance
     */
    abstract public function assignSource(Select $targetSelect, Database\Select $sourceSelect);
}
