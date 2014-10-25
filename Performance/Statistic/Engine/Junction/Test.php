<?php

namespace PM\Statistic\Engine\Junction;

use PM\Main\Database;
use PM\Statistic\Engine\Select;
use PM\Statistic\Enum\Source\Target;
use PM\Statistic\Engine\Exception;

/**
 * This script defines class for assign junction to target entity in statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Test extends AbstractJunction {

    /**
     * It creates junction to target entity.
     *
     * @param Select $select Statistic select instance
     * @param string $target One of enum \PM\Statistic\Enum\Source\Target
     *
     * @return Select
     */
    public function createJunction(Select $select, $target) {
        if ($select->hasJunction($target) === false) {
            switch ($target) {
                case Target::MEASURE:
                    $select->joinInner(
                        array($select->getTableName(Target::MEASURE) => 'test_measure'),
                        $select->getTableName(Target::TEST).'.id = '.$select->getTableName(Target::MEASURE).'.testId',
                        array()
                    );
                    break;
                case Target::SCENARIO:
                    $select->joinInner(
                        array($select->getTableName(Target::SCENARIO) => 'scenario'),
                        $select->getTableName(Target::SCENARIO).'.id = '.$select->getTableName(Target::TEST).'.scenarioId',
                        array()
                    );
                    break;
                default:
                    throw new Exception('Call has no junction to '.$target);
            }
        }

        return $this;
    }

    /**
     * It assings source select to statistic select.
     *
     * @param Select          $targetSelect Statistic select instance
     * @param Database\Select $sourceSelect Source select instance
     *
     * @return Select
     */
    public function assignSource(Select $targetSelect, Database\Select $sourceSelect) {
        if ($targetSelect->hasJunction(Target::TEST) === false) {
            throw new Exception('Target select doesn\'t have required table for entity '.Target::TEST.'.');
        }

        $targetSelect->joinInner(
            array($targetSelect->getTableName(self::SOURCE_TABLE) => $sourceSelect),
            $targetSelect->getTableName(Target::TEST).'.id = '.$targetSelect->getTableName(self::SOURCE_TABLE).'.id',
            array()
        );

        return $targetSelect;
    }
}
