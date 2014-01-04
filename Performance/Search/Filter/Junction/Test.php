<?php

namespace PF\Search\Filter\Junction;

use PF\Search\Filter\Select;
use PF\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines class for junction between test and condition of filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Test extends AbstractJunction {

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
        $this->url($select, $condition);
        $this->state($select, $condition);
        $this->started($select, $condition);
        $this->time($select, $condition);
        $this->calls($select, $condition);

        $condition->fulltext($select);
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
        $select->joinInner(array($alias => 'test_attempt'), $alias.'.testId = target.id', array('url'));

        $condition->addFilter($select, $alias, 'url');
    }

    /**
     * This method provider search by state.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function state(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'state');
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
        $condition->addFilter($select, 'target', 'started');
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
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'test_attempt'), $alias.'.testId = target.id', array('time'));

        $condition->addFilter($select, $alias, 'time');
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
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'test_attempt'), $alias.'.testId = target.id', array('calls'));

        $condition->addFilter($select, $alias, 'calls');
    }
}
