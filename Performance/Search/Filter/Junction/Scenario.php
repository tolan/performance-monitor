<?php

namespace PM\Search\Filter\Junction;

use PM\Search\Filter\Select;
use PM\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines class for junction between scenario and condition of filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Scenario extends AbstractJunction {

    /**
     * This method is called after is junction created. It sets group function.
     *
     * @param enum                                          $filterName One of PM\Search\Enum\Filter
     * @param \PM\Search\Filter\Select                      $select     Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function afterFilter($filterName, Select $select, AbstractCondition $condition) {
        $select->group('target.id');
    }

    /**
     * This method provider fulltext search.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function fulltext(Select $select, AbstractCondition $condition=null) {
        $this->name($select, $condition);
        $this->edited($select, $condition);
        $this->url($select, $condition);
        $this->started($select, $condition);
        $this->time($select, $condition);
        $this->calls($select, $condition);

        $condition->fulltext($select);
    }

    /**
     * This method provider search by name.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function name(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'name');
    }

    /**
     * This method provider search by edited.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function edited(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'edited');
    }

    /**
     * This method provider search by url.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function url(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'scenario_request'), $alias.'.scenarioId = target.id', array('url'));

        $condition->addFilter($select, $alias, 'url');
    }

    /**
     * This method provider search by started.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function started(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'scenario_test'), $alias.'.scenarioId = target.id', array('started'));

        $condition->addFilter($select, $alias, 'started');
    }

    /**
     * This method provider search by time.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function time(Select $select, AbstractCondition $condition) {
        $aliasTest    = $select->getUniqueTableAlias();
        $aliasMeasure = $select->getUniqueTableAlias();

        $select->joinInner(array($aliasTest => 'scenario_test'), $aliasTest.'.scenarioId = target.id', array());
        $select->joinInner(array($aliasMeasure => 'test_measure'), $aliasMeasure.'.testId = '.$aliasTest.'.id', array('time'));

        $condition->addFilter($select, $aliasMeasure, 'time');
    }

    /**
     * This method provider search by calls.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function calls(Select $select, AbstractCondition $condition) {
        $aliasTest    = $select->getUniqueTableAlias();
        $aliasMeasure = $select->getUniqueTableAlias();

        $select->joinInner(array($aliasTest => 'scenario_test'), $aliasTest.'.scenarioId = target.id', array());
        $select->joinInner(array($aliasMeasure => 'test_measure'), $aliasMeasure.'.testId = '.$aliasTest.'.id', array('calls'));

        $condition->addFilter($select, $aliasMeasure, 'calls');
    }
}
