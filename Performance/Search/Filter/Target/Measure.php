<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;

/**
 * This script defines class for target measure entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Measure extends AbstractTarget {

    /**
     * It sets table into select for measure entity.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'measure'));
    }
}