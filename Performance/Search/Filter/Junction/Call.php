<?php

namespace PM\Search\Filter\Junction;

use PM\Search\Filter\Select;
use PM\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines class for junction between call and condition of filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Call extends AbstractJunction {

    /**
     * This method is called after is junction created. It sets column function.
     *
     * @param enum                                          $filterName One of PM\Search\Enum\Filter
     * @param \PM\Search\Filter\Select                      $select     Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function afterFilter($filterName, Select $select, AbstractCondition $condition = null) {
        $select->columns(array('wholeTime' => '(target.time + target.timeSubStack)'));
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
        $this->file($select, $condition);
        $this->line($select, $condition);
        $this->content($select, $condition);
        $this->immersion($select, $condition);

        $condition->fulltext($select);
    }

    /**
     * This method provider search by file.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function file(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'file');
    }

    /**
     * This method provider search by line.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function line(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'line');
    }

    /**
     * This method provider search by content.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function content(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'content');
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
        $condition->addFilter($select, '(target', 'time + target.timeSubStack)');
    }

    /**
     * This method provider search by immersion.
     *
     * @param \PM\Search\Filter\Select                      $select    Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return void
     */
    protected function immersion(Select $select, AbstractCondition $condition) {
        $condition->addFilter($select, 'target', 'immersion');
    }
}
