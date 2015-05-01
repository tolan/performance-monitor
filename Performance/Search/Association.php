<?php

namespace PM\Search;

/**
 * This script defines class for association between all enums in search.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Association {
    const OPERATORS = 'operators';
    const TYPE      = 'type';
    const VALUES    = 'values';

    /**
     * It defines operators for standard filter types.
     *
     * @var array
     */
    private $_standards = array(
        Enum\Type::QUERY => array(),
        Enum\Type::STRING => array(
            self::OPERATORS => array(
                Enum\Operator::EQUAL,
                Enum\Operator::CONTAINS,
                Enum\Operator::DOES_NOT_CONTAINS
            )
        ),
        Enum\Type::DATE => array(
            self::OPERATORS => array(
                Enum\Operator::EQUAL,
                Enum\Operator::AFTER,
                Enum\Operator::BEFORE
            )
        ),
        Enum\Type::ENUM => array(
            self::OPERATORS => array(
                Enum\Operator::IN,
                Enum\Operator::NOT_IN,
                Enum\Operator::IS_SET
            )
        ),
        Enum\Type::FLOAT => array(
            self::OPERATORS => array(
                Enum\Operator::GREATER_THAN,
                Enum\Operator::LESS_THAN
            )
        ),
        Enum\Type::INT => array(
            self::OPERATORS => array(
                Enum\Operator::EQUAL,
                Enum\Operator::NOT_EQUAL,
                Enum\Operator::GREATER_THAN,
                Enum\Operator::LESS_THAN
            )
        )
    );

    /**
     * It defines enums names for filters.
     *
     * @var array
     */
    private $_enums = array(
        Enum\Target::TEST => array(
            Enum\Filter::STATE => '\PM\Profiler\Enum\TestState'
        ),
        Enum\Target::MEASURE => array(
            Enum\Filter::STATE  => '\PM\Profiler\Monitor\Storage\State',
            Enum\Filter::METHOD => '\PM\Main\Http\Enum\Method'
        ),
        Enum\Target::STATISTIC_SET => array(
            Enum\Filter::SOURCE  => array(
                Enum\Target::SCENARIO,
                Enum\Target::TEST,
                Enum\Target::MEASURE,
                Enum\Target::CALL
            )
        ),
        Enum\Target::STATISTIC_RUN => array(
            Enum\Filter::SOURCE  => array(
                Enum\Target::SCENARIO,
                Enum\Target::TEST,
                Enum\Target::MEASURE,
                Enum\Target::CALL
            ),
            Enum\Filter::STATE => '\PM\Statistic\Enum\Run\State'
        )
    );

    /**
     * It defines filter type for each filter and their entity.
     *
     * @var array
     */
    private $_association = array(
        Enum\Target::SCENARIO => array(
            Enum\Filter::FULLTEXT => Enum\Type::QUERY,
            Enum\Filter::NAME     => Enum\Type::STRING,
            Enum\Filter::EDITED   => Enum\Type::DATE,
            Enum\Filter::URL      => Enum\Type::STRING,
            Enum\Filter::STARTED  => Enum\Type::DATE,
            Enum\Filter::TIME     => Enum\Type::FLOAT,
            Enum\Filter::CALLS    => Enum\Type::INT
        ),
        Enum\Target::TEST => array(
            Enum\Filter::FULLTEXT => Enum\Type::QUERY,
            Enum\Filter::URL      => Enum\Type::STRING,
            Enum\Filter::STATE    => Enum\Type::ENUM,
            Enum\Filter::STARTED  => Enum\Type::DATE,
            Enum\Filter::TIME     => Enum\Type::FLOAT,
            Enum\Filter::CALLS    => Enum\Type::INT
        ),
        Enum\Target::MEASURE => array(
            Enum\Filter::FULLTEXT      => Enum\Type::QUERY,
            Enum\Filter::URL           => Enum\Type::STRING,
            Enum\Filter::STATE         => Enum\Type::ENUM,
            Enum\Filter::STARTED       => Enum\Type::DATE,
            Enum\Filter::METHOD        => Enum\Type::ENUM,
            Enum\Filter::TIME          => Enum\Type::FLOAT,
            Enum\Filter::CALLS         => Enum\Type::INT,
            Enum\Filter::FILE          => Enum\Type::STRING,
            Enum\Filter::LINE          => Enum\Type::INT,
            Enum\Filter::CONTENT       => Enum\Type::STRING,
            Enum\Filter::IMMERSION     => Enum\Type::INT
        ),
        Enum\Target::CALL => array(
            Enum\Filter::FULLTEXT      => Enum\Type::QUERY,
            Enum\Filter::FILE          => Enum\Type::STRING,
            Enum\Filter::LINE          => Enum\Type::INT,
            Enum\Filter::CONTENT       => Enum\Type::STRING,
            Enum\Filter::TIME          => Enum\Type::FLOAT,
            Enum\Filter::IMMERSION     => Enum\Type::INT
        ),
        Enum\Target::STATISTIC_SET => array(
            Enum\Filter::FULLTEXT => Enum\Type::QUERY,
            Enum\Filter::NAME     => Enum\Type::STRING,
            Enum\Filter::SOURCE   => Enum\Type::ENUM,
            Enum\Filter::STARTED  => Enum\Type::DATE
        ),
        Enum\Target::STATISTIC_RUN => array(
            Enum\Filter::FULLTEXT => Enum\Type::QUERY,
            Enum\Filter::SOURCE   => Enum\Type::ENUM,
            Enum\Filter::STATE    => Enum\Type::ENUM,
            Enum\Filter::STARTED  => Enum\Type::DATE
        )
    );

    /**
     * It defines that search target has allowed logic expression.
     *
     * @var array
     */
    private $_isAllowedLogic = array(
        Enum\Target::SCENARIO      => true,
        Enum\Target::TEST          => true,
        Enum\Target::MEASURE       => true,
        Enum\Target::CALL          => false,
        Enum\Target::STATISTIC_SET => true,
        Enum\Target::STATISTIC_RUN => true
    );

    /**
     * It defines which entities can be search by usage.
     *
     * @var array
     */
    private $_usageEntities = array(
        Enum\Usage::SEARCH    => array(
            Enum\Target::SCENARIO,
            Enum\Target::TEST,
            Enum\Target::CALL,
            Enum\Target::MEASURE,
            Enum\Target::STATISTIC_SET,
            Enum\Target::STATISTIC_RUN
        ),
        Enum\Usage::STATISTIC => array(
            Enum\Target::SCENARIO,
            Enum\Target::TEST,
            Enum\Target::CALL,
            Enum\Target::MEASURE
        ),
        Enum\Usage::CRON      => array(
            Enum\Target::SCENARIO,
            Enum\Target::STATISTIC_SET
        )
    );

    /**
     * Returns association for search menu.
     *
     * @param enum $usage One of enum Enum\Usage
     *
     * @return array
     */
    public function getMenu($usage) {
        $result = array();

        foreach ($this->_association as $entity => $assoc) {
            if (in_array($entity, $this->_usageEntities[$usage])) {
                $result[$entity] = $assoc;
            }
        }

        return $result;
    }

    /**
     * Returns that search target has allowed logic expression.
     *
     * @param enum $target One of enum \PM\Search\Enum\Target
     *
     * @return boolean
     */
    public function isAllowedLogic($target) {
        return $this->_isAllowedLogic[$target];
    }

    /**
     * Gets filter options by given target entity and filter name.
     *
     * @param enum $target One of Enum\Target
     * @param enum $filter One of Enum\Filter
     *
     * @return array
     */
    public function getFilter($target, $filter) {
        $result = $this->_association[$target][$filter];

        if (is_string($result)) {
            $result = $this->_getFilterOptions($target, $filter, $result);
        }

        $result['name']           = 'search.filter.'.$target.'.'.$filter;
        $result['target']         = $target;
        $result['filter']         = $filter;
        $result['isAllowedLogic'] = $this->isAllowedLogic($target);

        return $result;
    }

    /**
     * Helper method for extract options for standards filter.
     *
     * @param enum $target One of Enum\Target
     * @param enum $filter One of Enum\Filter
     * @param enum $type   One of Enum\Type
     *
     * @return array
     */
    private function _getFilterOptions($target, $filter, $type) {
        $newOptions = $this->_standards[$type];

        if (isset($newOptions[self::OPERATORS])) {
            foreach ($newOptions[self::OPERATORS] as $key => $operator) {
                $newOptions[self::OPERATORS][$key] = array(
                    'value' => $operator,
                    'text'  => 'search.filter.operator.'.str_replace(' ', '.', $operator)
                );
            }
        }

        $newOptions[self::TYPE] = $type;
        if ($type === Enum\Type::ENUM) {
            $enum      = $this->_enums[$target][$filter];
            if (is_array($enum)) {
                $constants = $enum;
            } else {
                $constants = $enum::getConstants();
            }

            foreach ($constants as $const) {
                $newOptions[self::VALUES][] = array(
                    'value' => $const,
                    'name'  => 'search.filter.'.$target.'.'.$filter.'.'.$const
                );
            }
        }

        return $newOptions;
    }
}
