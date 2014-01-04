<?php

namespace PF\Search\Component;

use PF\Search\Enum;

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
        Enum\Target::MEASURE => array(
            Enum\Filter::METHOD => 'Performance_Main_Http_Enum_Method'
        ),
        Enum\Target::TEST => array(
            Enum\Filter::STATE => 'Performance_Profiler_Enum_AttemptState'
        ),
        Enum\Target::ATTEMPT =>array(
            Enum\Filter::STATE  => 'Performance_Profiler_Enum_AttemptState',
            Enum\Filter::METHOD => 'Performance_Main_Http_Enum_Method'
        )
    );

    /**
     * It defines filter type for each filter and their entity.
     *
     * @var array
     */
    private $_association = array(
        Enum\Target::MEASURE => array(
            Enum\Filter::FULLTEXT => Enum\Type::QUERY,
            Enum\Filter::NAME     => Enum\Type::STRING,
            Enum\Filter::EDITED   => Enum\Type::DATE,
            Enum\Filter::METHOD   => Enum\Type::ENUM,
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
        Enum\Target::ATTEMPT => array(
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
        )
    );

    /**
     * Returns association for search menu.
     *
     * @return array
     */
    public function getMenu() {
        return $this->_association;
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

        $result['name']   = 'search.filter.'.$target.'.'.$filter;
        $result['target'] = $target;
        $result['filter'] = $filter;

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
            $constants = $enum::getConstants();

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
