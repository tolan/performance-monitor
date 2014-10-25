<?php

namespace PM\Profiler\Monitor\Filter;

use PM\Profiler\Monitor\Filter\Enum\Operator;
use PM\Profiler\Monitor\Filter\Enum\Parameter;

/**
 * This script defines association class between search parameter and operator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Association {

    /**
     * Map between parameter and operator.
     *
     * @var array
     */
    private static $_map = array(
        Parameter::FILE => array(
            Operator::REG_EXP
        ),
        Parameter::IMMERSION => array(
            Operator::LOWER_THAN,
            Operator::HIGHER_THAN
        ),
        Parameter::LINE => array(
            Operator::LOWER_THAN,
            Operator::HIGHER_THAN
        ),
        Parameter::SUB_STACK => array(
            Operator::BOOLEAN
        )
    );

    /**
     * Gets associations between parameters and operators in structure for fclient selection.
     *
     * @return array
     */
    public static function getAssociation() {
        $selection = Parameter::getSelection('profiler.scenario.request.filter.parameter.');
        $operators = Operator::getSelection('profiler.scenario.request.filter.parameter.operator.');
        foreach ($operators as $key => $operator) {
            unset($operators[$key]);
            $operators[$operator['value']] = $operator;
        }

        foreach ($selection as &$options) {
            if (array_key_exists($options['value'], self::$_map)) {
                $ops                  = self::$_map[$options['value']];
                $options['operators'] = array();

                foreach ($ops as $op) {
                    $options['operators'][] = $operators[$op];
                }
            }
        }

        return $selection;
    }
}
