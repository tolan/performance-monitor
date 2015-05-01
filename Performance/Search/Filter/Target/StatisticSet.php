<?php

namespace PM\Search\Filter\Target;

use PM\Search\Filter\Select;
use PM\Search\Enum\Format;

/**
 * This script defines class for target statistic set entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class StatisticSet extends AbstractTarget {

    /**
     * Format list.
     *
     * @var array
     */
    protected $_format = array(
        'id'         => Format::INT,
        'started'    => Format::DATETIME,
        'templateId' => Format::INT
    );

    /**
     * It sets table into select for statistic set entity.
     *
     * @param Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $select->from(array('target' => 'statistic_set'));
    }
}