<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;

/**
 * This script defines class for target test entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Test extends AbstractTarget {

    /**
     * It sets table into select for test entity.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'measure_test'));
    }
}