<?php

namespace PF\Search\Filter\Target;

use PF\Search\Filter\Select;
use PF\Search\Enum\Format;

/**
 * This script defines class for target measure entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Measure extends AbstractTarget {

    /**
     * Format list.
     *
     * @var array
     */
    protected $_format = array(
        'testId'  => Format::INT,
        'started' => Format::DATETIME,
        'time'    => Format::FLOAT,
        'calls'   => Format::INT,
        'line'    => Format::INT
    );

    /**
     * It sets table into select for measure entity.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'test_measure'));
    }
}
