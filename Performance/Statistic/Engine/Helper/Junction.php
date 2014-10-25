<?php

namespace PM\Statistic\Engine\Helper;

use PM\Main\Provider;
use PM\Statistic\Engine;
use PM\Main\Database;
use PM\Statistic\Enum\Source;

/**
 * This script defines helper class for create junctions between two entities.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Junction {

    /**
     * Map between entities.
     *
     * @var array
     */
    private static $_entitiesJoinMap = array(
        Source\Target::CALL => array(
            Source\Target::CALL     => Source\Target::CALL,
            Source\Target::MEASURE  => Source\Target::MEASURE,
            Source\Target::TEST     => Source\Target::MEASURE,
            Source\Target::SCENARIO => Source\Target::MEASURE
        ),
        Source\Target::MEASURE => array(
            Source\Target::CALL     => Source\Target::CALL,
            Source\Target::MEASURE  => Source\Target::MEASURE,
            Source\Target::TEST     => Source\Target::TEST,
            Source\Target::SCENARIO => Source\Target::TEST
        ),
        Source\Target::TEST => array(
            Source\Target::CALL     => Source\Target::MEASURE,
            Source\Target::MEASURE  => Source\Target::MEASURE,
            Source\Target::TEST     => Source\Target::TEST,
            Source\Target::SCENARIO => Source\Target::SCENARIO
        ),
        Source\Target::SCENARIO => array(
            Source\Target::CALL     => Source\Target::TEST,
            Source\Target::MEASURE  => Source\Target::TEST,
            Source\Target::TEST     => Source\Target::TEST,
            Source\Target::SCENARIO => Source\Target::SCENARIO
        )
    );

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * It provides creation junctions between two entites in statistic select.
     *
     * @param Engine\Select $select      Statistic select instance
     * @param string        $source      One of Source\Target
     * @param string        $destination One of Source\Target
     *
     * @return Engine\Select
     */
    public function createJunctions(Engine\Select $select, $source, $destination) {
        while (!$select->hasJunction($destination)) {
            $next = $this->_getJunctionEntity($source, $destination);

            $junction = $this->_provider->get('PM\Statistic\Engine\Junction\\'.ucfirst($source));
            /* @var $junction \PM\Statistic\Engine\Junction\AbstractJunction */
            $junction->createJunction($select, $next);

            $source = $next;
        }

        return $select;
    }

    /**
     * It provides assign source select into statistic select by source entity.
     *
     * @param Engine\Select   $select       Statistic select
     * @param Database\Select $sourceSelect Source select with ids for source
     * @param string          $source       Source entity for source select, one of enum Source\Target
     *
     * @return Engine\Select
     */
    public function assignSource(Engine\Select $select, Database\Select $sourceSelect, $source) {
        $junction = $this->_provider->get('PM\Statistic\Engine\Junction\\'.ucfirst($source));
        /* @var $junction \PM\Statistic\Engine\Junction\AbstractJunction */
        $junction->assignSource($select, $sourceSelect);

        return $select;
    }

    /**
     * Returns next junction entity on way from source entity to destination entity.
     *
     * @param string $source      One of enum Source\Target
     * @param string $destination One of enum Source\Target
     *
     * @return string One of enum Source\Target
     */
    private function _getJunctionEntity($source, $destination) {
        $entity = null;

        if (array_key_exists($source, self::$_entitiesJoinMap) && array_key_exists($destination, self::$_entitiesJoinMap[$source])) {
            $entity = self::$_entitiesJoinMap[$source][$destination];
        }

        return $entity;
    }
}
