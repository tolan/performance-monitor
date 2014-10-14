<?php

namespace PF\Search\Filter\Junction;

use PF\Search\Filter\Select;
use PF\Search\Filter\Condition\AbstractCondition;

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
     * @param enum                                          $filterName One of PF\Search\Enum\Filter
     * @param \PF\Search\Filter\Select                      $select     Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return \PF\Search\Filter\Junction\AbstractJunction
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
     * @param enum                                          $filterName One of PF\Search\Enum\Filter
     * @param \PF\Search\Filter\Select                      $select     Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function beforeFilter($filterName, Select $select, AbstractCondition $condition = null) {
    }

    /**
     * This method is called after junction was created.
     *
     * @param enum                                          $filterName One of PF\Search\Enum\Filter
     * @param \PF\Search\Filter\Select                      $select     Select instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition  Condition instance
     *
     * @return void
     */
    protected function afterFilter($filterName, Select $select, AbstractCondition $condition = null) {
    }
}
