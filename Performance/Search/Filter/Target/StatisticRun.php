<?php

namespace PM\Search\Filter\Target;

use PM\Search\Filter\Select;
use PM\Search\Enum\Format;

/**
 * This script defines class for target statistic run entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class StatisticRun extends AbstractTarget {

    /**
     * Format list.
     *
     * @var array
     */
    protected $_format = array(
        'id'             => Format::INT,
        'statisticSetId' => Format::INT,
        'started'        => Format::DATETIME,
        'templateId'     => Format::INT
    );

    /**
     * It sets table into select for statistic run entity.
     *
     * @param Select $select Select instnace
     *
     * @return void
     */
    public function setTarget(Select $select) {
        $alias = $select->getUniqueTableAlias('info');

        $select->from(array('target' => 'statistic_set_run'));
        $select->joinInner(array($alias => 'statistic_set'), $alias.'.id = target.statisticSetId', array('name' , 'description'));
    }
}