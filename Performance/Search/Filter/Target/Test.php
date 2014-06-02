<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;
use PF\Search\Enum\Format;

/**
 * This script defines class for target test entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Test extends AbstractTarget {

    /**
     * Format list.
     *
     * @var array
     */
    protected $_format = array(
        'scenarioId' => Format::INT,
        'started'    => Format::DATETIME,
        'time'       => Format::FLOAT,
        'calls'      => Format::INT
    );

    /**
     * It sets table into select for test entity.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'scenario_test'));
    }
}