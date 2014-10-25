<?php

namespace PM\Search\Filter\Target;

use PM\Search\Filter\Select;
use PM\Search\Enum\Format;

/**
 * This script defines class for target call entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Call extends AbstractTarget {

    /**
     * Format list.
     *
     * @var array
     */
    protected $_format = array(
        'measureId'    => Format::INT,
        'parentId'     => Format::INT,
        'line'         => Format::INT,
        'lines'        => Format::INT,
        'time'         => Format::FLOAT,
        'timeSubStack' => Format::FLOAT,
        'wholeTime'    => Format::FLOAT,
        'immersion'    => Format::INT
    );

    /**
     * It sets table into select for call entity.
     *
     * @param \PM\Search\Filter\Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'measure_statistic_data'));
    }
}