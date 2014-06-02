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
        $this->url($select, $condition);
        $this->state($select, $condition);
        $this->started($select, $condition);
        $this->method($select, $condition);
        $this->time($select, $condition);
        $this->calls($select, $condition);
        $this->file($select, $condition);

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
        $condition->addFilter($select, 'target', 'url');
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
     * This method provider search by method.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function method(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'method');
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
        $condition->addFilter($select, 'target', 'time');
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
        $condition->addFilter($select, 'target', 'calls');
    }

    /**
     * This method provider search by file.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function file(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_statistic_data'), 'target.id = '.$alias.'.measureId', array('file'));
        $condition->addFilter($select, $alias, 'file');
    }

    /**
     * This method provider search by line.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function line(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_statistic_data'), 'target.id = '.$alias.'.measureId', array('line'));
        $condition->addFilter($select, $alias, 'line');
    }

    /**
     * This method provider search by content.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function content(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_statistic_data'), 'target.id = '.$alias.'.measureId', array('content'));
        $condition->addFilter($select, $alias, 'content');
    }

    /**
     * This method provider search by immersion.
     *
     * @param \PF\Search\Filter\Select                      $select    Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function immersion(Select $select, AbstractCondition $condition) {
        $alias = $select->getUniqueTableAlias();
        $select->joinInner(array($alias => 'measure_statistic_data'), 'target.id = '.$alias.'.measureId', array('immersion'));
        $condition->addFilter($select, $alias, 'immersion');
    }
}
