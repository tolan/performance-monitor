<?php

namespace PM\Search\Filter\Junction;

use PM\Search\Filter\Select;
use PM\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines class for junction between statistic set and condition of filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class StatisticSet extends AbstractJunction {

    /**
     * This method is called after is junction created. It sets group function.
     *
     * @param string            $filterName One of PM\Search\Enum\Filter
     * @param Select            $select     Select instance
     * @param AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function afterFilter($filterName, Select $select, AbstractCondition $condition) {
        $select->group('target.id');
    }

    /**
     * This method provider fulltext search.
     *
     * @param Select            $select    Select instance
     * @param AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function fulltext(Select $select, AbstractCondition $condition=null) {
        $this->name($select, $condition);
        $this->source($select, $condition);
        $this->started($select, $condition);

        $condition->fulltext($select);
    }

    /**
     * This method provider search by name.
     *
     * @param Select            $select    Select instance
     * @param AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function name(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'name');
    }

    /**
     * This method provider search by source.
     *
     * @param Select            $select    Select instance
     * @param AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function source(Select $select, AbstractCondition $condition) {
        $aliasJunction = $select->getUniqueTableAlias();
        $alias         = $select->getUniqueTableAlias();

        $select->joinInner(array($aliasJunction => 'statistic_set_template'), 'target.id = '.$aliasJunction.'.statisticSetId', array());
        $select->joinInner(
            array($alias => 'statistic_template'),
            $alias.'.id = '.$aliasJunction.'.statisticTemplateId',
            array('templateId' => 'id' ,'sourceType')
        );

        $condition->addFilter($select, $alias, 'sourceType');
    }

    /**
     * This method provider search by started.
     *
     * @param Select            $select    Select instance
     * @param AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function started(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'statistic_set_run'), 'target.id = '.$alias.'.statisticSetId', array('started'));

        $condition->addFilter($select, $alias, 'started');
    }
}
