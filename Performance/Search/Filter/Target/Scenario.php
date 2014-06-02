<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;
use PF\Search\Enum\Format;

/**
 * This script defines class for target scenario entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Scenario extends AbstractTarget {

    /**
     * Format list.
     *
     * @var array
     */
    protected $_format = array(
        'edited'  => Format::DATETIME,
        'started' => Format::DATETIME,
        'time'    => Format::FLOAT,
        'calls'   => Format::INT
    );

    /**
     * It sets table into select for scenario entity.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'scenario'));
    }
}