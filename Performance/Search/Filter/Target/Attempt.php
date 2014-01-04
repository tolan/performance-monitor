<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;

/**
 * This script defines class for target attempt entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Attempt extends AbstractTarget {

    /**
     * It sets table into select for attempt entity.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'test_attempt'));
    }
}
