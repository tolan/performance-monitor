<?php

namespace PM\Search\Filter;

use PM\Main\Database;
use PM\Main\Log;
use PM\Search\Filter\Select;
use PM\Search\Filter\Target\AbstractTarget;
use PM\Search\Filter\Junction\AbstractJunction;
use PM\Search\Filter\Condition\AbstractCondition;

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
     * @var \PM\Search\Filter\Select
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
     * It means whether container was compiled.
     *
     * @var boolean
     */
    private $_compiled = false;

    /**
     * Construct method.
     *
     * @param \PM\Main\Database $database Database instnace
     * @param \PM\Main\Log      $log      Log instnace
     *
     * @return void
     */
    public function __construct(Database $database, Log $log) {
        $connection = $database->getConnection();

        $select = new Select($connection, $log);

        $this->_select = $select;
    }

    /**
     * Sets one target for search entity by fiters.
     *
     * @param \PM\Search\Filter\Target\AbstractTarget $target Target instnance
     *
     * @return \PM\Search\Filter\Container
     *
     * @throws Exception Throws when you try call this method second.
     */
    public function setTarget(AbstractTarget $target) {
        if ($this->_target !== null) {
            throw new Exception('You can not set target again.');
        }

        if ($this->_compiled === true) {
            throw new Exception('You can not set target again because container is compiled.');
        }

        $this->_target = $target;

        return $this;
    }

    /**
     * It adds filter by given filter parameters, junction instance and condition instnace.
     *
     * @param array                                         $filter    Filter parameters
     * @param \PM\Search\Filter\Junction\AbstractJunction   $junction  Junction instance
     * @param \PM\Search\Filter\Condition\AbstractCondition $condition Condition instance
     *
     * @return \PM\Search\Filter\Container
     */
    public function addFilter($filter, AbstractJunction $junction, AbstractCondition $condition) {
        if ($this->_compiled === true) {
            throw new Exception('Filter can\'t be added because container is compiled.');
        }

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
     * Returns compiled searching select.
     *
     * @return \PM\Search\Filter\Select
     */
    public function getSelect() {
        $this->_compile($this->_select, $this->_target, $this->_filters);

        return $this->_select;
    }

    /**
     * Returns compiled select and converted to string.
     *
     * @return string
     */
    public function __toString() {
        $this->_compile($this->_select, $this->_target, $this->_filters);

        return $this->_select->assemble();
    }

    /**
     * It compile target and all filters to one statement for database select.
     *
     * @param \PM\Search\Filter\Select                $select  Select instnace
     * @param \PM\Search\Filter\Target\AbstractTarget $target  Target instnace
     * @param array                                   $filters Sets of filters
     *
     * @return void
     */
    private function _compile(Select $select, AbstractTarget $target, $filters) {
        if ($this->_compiled === false) {
            $target->setTarget($select);

            foreach ($filters as $filter) {
                $condition = $filter[self::CONDITION];
                $junction  = $filter[self::JUNCTION];

                $condition->prepareFilter($filter[self::FILTER]['operator'], $filter[self::FILTER]['value']);
                $junction->prepareJunction($filter[self::FILTER]['filter'], $select, $condition);
            }

            $this->_compiled = true;
        }

        return $this;
    }
}
