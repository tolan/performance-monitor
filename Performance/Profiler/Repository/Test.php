<?php

namespace PF\Profiler\Repository;

use PF\Main\Abstracts\Repository;
use PF\Profiler\Entity;
use PF\Profiler\Enum;

/**
 * This script defines class for test repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Test extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('scenario_test');
    }

    /**
     * Returns tests for scenario.
     *
     * @param int $scenarioId ID of scenario
     *
     * @return \PF\Profiler\Entity\Test
     */
    public function getTestsForScenario($scenarioId) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('scenarioId = ?', $scenarioId);

        $data   = $select->fetchAll();
        $result = array();

        foreach ($data as $item) {
            $item['id']         = (int)$item['id'];
            $item['scenarioId'] = (int)$item['scenarioId'];
            $item['started']    = $this->getUtils()->convertTimeFromMySQLDateTime($item['started']);

            $result[] = new Entity\Test($item);
        }

        return $result;
    }

    /**
     * Returns test entity by given ID.
     *
     * @param int $id ID of test
     *
     * @return \PF\Profiler\Entity\Test
     */
    public function getTest($id) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('id = ?', $id);

        $data = $select->fetchOne();

        return new Entity\Test($data);
    }

    /*
     * Create new scenario with given ID.
     *
     * @param int $scenarioId ID of new scenario
     *
     * @return \PF\Profiler\Entity\Test
     */
    public function create($scenarioId) {
        $data = array(
            'scenarioId' => $scenarioId,
            'state'      => Enum\TestState::STATE_IDLE,
            'started'    => $this->getUtils()->convertTimeToMySQLDateTime()
        );

        $data['id'] = parent::create($data);

        return new Entity\Test($data);
    }

    /**
     * Updates scenario entity.
     *
     * @param int                      $id   ID of test
     * @param \PF\Profiler\Entity\Test $test Test entity
     *
     * @return boolean
     */
    public function update($id, Entity\Test $test) {
        parent::update($id, $test->toArray());

        return true;
    }

    /**
     * Deletes scenario entity by given ID.
     *
     * @param int $testId ID of test
     * 
     * @return boolean
     */
    public function delete($testId) {
        parent::delete($testId);

        return true;
    }
}
