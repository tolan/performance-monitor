<?php

namespace PF\Search\Filter\Junction;

use PF\Search\Filter\Select;
use PF\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines class for junction between measure and condition of filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Measure extends AbstractJunction {

    /**
     * This method is called after is junction created. It sets group function.
     *
     * @param enum                                          $filterName One of PF\Search\Enum\Filter
     * @param \PF\Search\Filter\Select                      $select     Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function afterFilter($filterName, Select $select, AbstractCondition $condition) {
        $select->group('target.id');
    }

    /**
     * This method provider fulltext search.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function fulltext(Select $select, AbstractCondition $condition=null) {
        $this->name($select, $condition);
        $this->edited($select, $condition);
        $this->method($select, $condition);
        $this->url($select, $condition);
        $this->started($select, $condition);
        $this->calls($select, $condition);

        $condition->fulltext($select);
    }

    /**
     * This method provider search by name.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function name(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'name');
    }

    /**
     * This method provider search by edited.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function edited(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'edited');
    }

    /**
     * This method provider search by method.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function method(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_request'), $alias.'.measureId = target.id', array('method'));

        $condition->addFilter($select, $alias, 'method');
    }

    /**
     * This method provider search by url.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function url(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_request'), $alias.'.measureId = target.id', array('url'));

        $condition->addFilter($select, $alias, 'url');
    }

    /**
     * This method provider search by started.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function started(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_test'), $alias.'.measureId = target.id', array('started'));

        $condition->addFilter($select, $alias, 'started');
    }

    /**
     * This method provider search by time.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function time(Select $select, AbstractCondition $condition) {
        $aliasTest    = $select->getUniqueTableAlias();
        $aliasAttempt = $select->getUniqueTableAlias();

        $select->joinInner(array($aliasTest => 'measure_test'), $aliasTest.'.measureId = target.id', array());
        $select->joinInner(array($aliasAttempt => 'test_attempt'), $aliasAttempt.'.testId = '.$aliasTest.'.id', array('time'));

        $condition->addFilter($select, $aliasAttempt, 'time');
    }

    /**
     * This method provider search by calls.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function calls(Select $select, AbstractCondition $condition) {
        $aliasTest    = $select->getUniqueTableAlias();
        $aliasAttempt = $select->getUniqueTableAlias();

        $select->joinInner(array($aliasTest => 'measure_test'), $aliasTest.'.measureId = target.id', array());
        $select->joinInner(array($aliasAttempt => 'test_attempt'), $aliasAttempt.'.testId = '.$aliasTest.'.id', array('calls'));

        $condition->addFilter($select, $aliasAttempt, 'calls');
    }
}
