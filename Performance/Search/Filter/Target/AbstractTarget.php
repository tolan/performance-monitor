<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;

/**
 * This script defines abstract class for target entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
abstract class AbstractTarget {

    /**
     * Abstract method for define and set target table for entity.
     *
     * @param PF\Search\Filter\Select $select Select instance
     */
    abstract public function setTarget(Select $select);

}
