<?php

namespace PF\Search\Filter;

use PF\Search\Filter\Select;
use PF\Search\Filter\Target\AbstractTarget;
use PF\Search\Filter\Junction\AbstractJunction;
use PF\Search\Filter\Condition\AbstractCondition;

/**
 * This script defines class for container of one target entity and set of filters.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Container {
    const FILTER    = 'filter';
    const JUNCTION  = 'junction';
    const CONDITION = 'condition';

    /**
     * Select instance.
     *
     * @var \PF\Search\Filter\Select
     */
    private $_select;

    /**
     * Target instance.
     *
     * @var AbstractTarget
     */
    private $_target = null;

    /**
     * Array of filters.
     *
     * @var array
     */
    private $_filters = array();

    /**
     * Construct method.
     *
     * @param \PF\Search\Filter\Select $select Select instnace
     */
    public function __construct(Select $select) {
        $this->_select = $select;
    }

    /**
     * Sets one target for search entity by fiters.
     *
     * @param \PF\Search\Filter\Target\AbstractTarget $target Target instnance
     *
     * @return \PF\Search\Filter\Container
     *
     * @throws Exception Throws when you try call this method second.
     */
    public function setTarget(AbstractTarget $target) {
        if ($this->_target !== null) {
            throw new Exception('You can not set target again.');
        }

        $this->_target = $target;

        return $this;
    }

    /**
     * It adds filter by given filter parameters, junction instance and condition instnace.
     *
     * @param array                                         $filter    Filter parameters
     * @param \PF\Search\Filter\Junction\AbstractJunction   $junction  Junction instance
     * @param \PF\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return \PF\Search\Filter\Container
     */
    public function addFilter($filter, AbstractJunction $junction, AbstractCondition $condition) {
        $this->_filters[] = array(
            self::FILTER    => $filter,
            self::JUNCTION  => $junction,
            self::CONDITION => $condition
        );

        return $this;
    }

    /**
     * Return all entities by given filters.
     *
     * @return array
     */
    public function fetchAll() {
        $this->_compile($this->_select, $this->_target, $this->_filters);

        $data = $this->_select->fetchAll();

        return $this->_target->format($data);
    }

    /**
     * It compile target and all filters to one statement for database select.
     *
     * @param \PF\Search\Filter\Select                $select  Select instnace
     * @param \PF\Search\Filter\Target\AbstractTarget $target  Target instnace
     * @param array                                   $filters Sets of filters
     *
     * @return void
     */
    private function _compile(Select $select, AbstractTarget $target, $filters) {
        $target->setTarget($select);

        foreach ($filters as $filter) {
            $condition = $filter[self::CONDITION];
            $junction  = $filter[self::JUNCTION];

            $condition->prepareFilter($filter[self::FILTER]['operator'], $filter[self::FILTER]['value']);
            $junction->prepareJunction($filter[self::FILTER]['filter'], $select, $condition);
        }
    }
}
