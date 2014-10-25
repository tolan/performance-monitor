<?php

namespace PM\Statistic;

use PM\Statistic\Enum\View;
use PM\Statistic\Enum\Source;

/**
 * This script defines class for association between all enums in statistic template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Association {

    /**
     * It defines data type of data attribute.
     *
     * @var array
     */
    private static $_dataTypeMap = array(
        View\Data::CALLS   => View\DataType::RANGE_INT,
        View\Data::CONTENT => View\DataType::REG_EXP,
        View\Data::FILE    => View\DataType::REG_EXP,
        View\Data::METHOD  => View\DataType::ENUM,
        View\Data::TIME    => View\DataType::RANGE_INT,
        View\Data::URL     => View\DataType::REG_EXP
    );

    /**
     * It defines list of function for each data attribute.
     *
     * @var array
     */
    private static $_dataFunctionsMap = array(
        View\Data::CALLS   => array(
            View\Functions::AVERAGE,
            View\Functions::SUM
        ),
        View\Data::CONTENT => array(
            View\Functions::COUNT
        ),
        View\Data::FILE    => array(
            View\Functions::COUNT
        ),
        View\Data::METHOD  => array(
            View\Functions::COUNT
        ),
        View\Data::TIME    => array(
            View\Functions::AVERAGE,
            View\Functions::MAX,
            View\Functions::SUM
        ),
        View\Data::URL     => array(
            View\Functions::COUNT
        )
    );

    /**
     * It defines list of data attribute for each target entity.
     *
     * @var array
     */
    private static $_targetDataMap = array(
        Source\Target::CALL => array(
            View\Data::CONTENT,
            View\Data::FILE,
            View\Data::TIME
        ),
        Source\Target::MEASURE => array(
            View\Data::CALLS,
            View\Data::METHOD,
            View\Data::TIME,
            View\Data::URL
        ),
        Source\Target::TEST => array(
            View\Data::CALLS,
            View\Data::METHOD,
            View\Data::URL
        ),
        Source\Target::SCENARIO => array(
            View\Data::URL
        )
    );

    /**
     * It defines list of target sub-entities for each target entity.
     *
     * @var array
     */
    private static $_targetSubEntities = array(
        Source\Target::CALL     => array(Source\Target::CALL),
        Source\Target::MEASURE  => array(Source\Target::MEASURE, Source\Target::CALL),
        Source\Target::TEST     => array(Source\Target::TEST, Source\Target::MEASURE, Source\Target::CALL),
        Source\Target::SCENARIO => array(Source\Target::SCENARIO, Source\Target::TEST, Source\Target::MEASURE, Source\Target::CALL)
    );

    /**
     * List of enum for data attributes with enum data type.
     *
     * @var array
     */
    private static $_enumMap = array(
        View\Data::METHOD => '\PM\Main\Http\Enum\Method'
    );

    /**
     * Returns configuration, maps and referencies for fclient processing.
     *
     * @return array
     */
    public function getConfig() {
        $result = array();

        $result['entitiesMenu'] = $this->_generateEntitiesMenu();
        $result['linesMenu']    = $this->_generateLinesMenu();
        $result['lineTypeMap']  = self::$_dataTypeMap;
        $result['enumMap']      = $this->_generateEnumMap();
        $result['graphTypes']   = View\Base::getSelection('statistic.view.type.');

        return $result;
    }

    /**
     * Returns structure for menu of target entities and their sub-entities.
     *
     * @return array
     */
    private function _generateEntitiesMenu() {
        $entities = self::$_targetSubEntities;
        $result   = array();

        foreach ($entities as $entity => $subEntities) {
            foreach ($subEntities as $subEntity) {
                $result[$entity][] = array(
                    'text'  => 'statistic.view.entity.'.$subEntity,
                    'value' => $subEntity
                );
            }
        }

        return $result;
    }

    /**
     * Returns structure for menu of line for each target entity.
     *
     * @return array
     */
    private function _generateLinesMenu() {
        $lines     = self::$_targetDataMap;
        $functions = self::$_dataFunctionsMap;
        $result    = array();

        foreach ($lines as $entity => $types) {
            foreach ($types as $type) {
                $subMenu = array();

                foreach ($functions[$type] as $function) {
                    $subMenu[] = array(
                        'text'     => 'statistic.view.line.function.'.$function,
                        'value'    => $type.'.'.$function,
                        'type'     => $type,
                        'function' => $function
                    );
                }

                $result[$entity][] = array(
                    'text'    => 'statistic.view.line.type.'.$type,
                    'submenu' => $subMenu
                );
            }
        }

        return $result;
    }

    /**
     * Returns selections for enums.
     *
     * @return array
     */
    private function _generateEnumMap() {
        $map    = self::$_enumMap;
        $result = array();

        foreach ($map as $type => $enum) {
            /* @var $enum \PM\Main\Abstracts\Enum */
            $result[$type] = $enum::getSelection('statistic.view.line.'.$type.'.');
        }

        return $result;
    }
}
