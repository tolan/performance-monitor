<?php

namespace PM\Search\Filter\Junction;

use PM\Search\Filter\Select;
use PM\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines abstract class for junction between target entity and condition.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
abstract class AbstractJunction {

    /**
     * It prepare junction filter and construct select to database.
     *
     * @param enum                                          $filterName One of PM\Search\Enum\Filter
     * @param \PM\Search\Filter\Select                      $select     Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return \PM\Search\Filter\Junction\AbstractJunction
     */
    public function prepareJunction($filterName, Select $select, AbstractCondition $condition = null) {
        $this->beforeFilter($filterName, $select, $condition);
        $this->$filterName($select, $condition);
        $this->afterFilter($filterName, $select, $condition);

        return $this;
    }

    /**
     * This method is called before junction is created.
     *
     * @param enum                                          $filterName One of PM\Search\Enum\Filter
     * @param \PM\Search\Filter\Select                      $select     Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function beforeFilter($filterName, Select $select, AbstractCondition $condition = null) {
    }

    /**
     * This method is called after junction was created.
     *
     * @param enum                                          $filterName One of PM\Search\Enum\Filter
     * @param \PM\Search\Filter\Select                      $select     Select instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function afterFilter($filterName, Select $select, AbstractCondition $condition = null) {
    }
}
