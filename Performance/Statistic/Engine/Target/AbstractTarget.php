<?php

namespace PM\Statistic\Engine\Target;

use PM\Main\Database\Select;

/**
 * This script defines abstract class for define target entity to statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
abstract class AbstractTarget {

    /**
     * Abstract method for define target entity.
     *
     * @param Select $select Statistic select instance
     */
    abstract public function setTarget(Select $select);
}
