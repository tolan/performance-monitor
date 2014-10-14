<?php

namespace PF\Statistic\Engine\Junction;

use PF\Main\Database;
use PF\Statistic\Engine\Select;
use PF\Statistic\Enum\Source\Target;
use PF\Statistic\Engine\Exception;

/**
 * This script defines class for assign junction to target entity in statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Scenario extends AbstractJunction {

    /**
     * It creates junction to target entity.
     *
     * @param Select $select Statistic select instance
     * @param string $target One of enum \PF\Statistic\Enum\Source\Target
     *
     * @return Select
     */
    public function createJunction(Select $select, $target) {
        if ($select->hasJunction($target) === false) {
            switch ($target) {
                case Target::TEST:
                    $select->joinInner(
                        array($select->getTableName(Target::TEST) => 'scenario_test'),
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
        if ($targetSelect->hasJunction(Target::SCENARIO) === false) {
            throw new Exception('Target select doesn\'t have required table for entity '.Target::SCENARIO.'.');
        }

        $targetSelect->joinInner(
            array($targetSelect->getTableName(self::SOURCE_TABLE) => $sourceSelect),
            $targetSelect->getTableName(Target::SCENARIO).'.id = '.$targetSelect->getTableName(self::SOURCE_TABLE).'.id',
            array()
        );

        return $targetSelect;
    }
}
