<?php

namespace PF\Profiler\Service;

use PF\Main\Abstracts\Service;
use PF\Main\Database;
use PF\Profiler\Repository;
use PF\Profiler\Enum\TestState;
use PF\Profiler\Monitor\Storage\State;
use PF\Profiler\Entity;

/**
 * This script defines class for test service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Test extends Service {

    /**
     * Returns tests for scenario.
     *
     * @param int                          $scenarioId ID of scenario
     * @param \PF\Profiler\Repository\Test $repository Repository test instance
     *
     * @return array
     */
    public function getTestsForScenario($scenarioId, Repository\Test $repository) {
        return $repository->getTestsForScenario($scenarioId);
    }

    /**
     * Returns test entity instance.
     *
     * @param int                          $testId     ID of test
     * @param \PF\Profiler\Repository\Test $repository Repository test instance
     *
     * @return \PF\Profiler\Entity\Test
     */
    public function getTest($testId, Repository\Test $repository) {
        return $repository->getTest($testId);
    }

    /**
     * Deletes test by given ID.
     *
     * @param int                          $testId     ID of test
     * @param \PF\Profiler\Repository\Test $repository Repository test instance
     * @param \PF\Main\Database            $database   Database instance
     *
     * @return boolean
     *
     * @throws \PF\Profiler\Service\Exception
     */
    public function deleteTest($testId, Repository\Test $repository, Database $database) {
        $transaction = $database->getTransaction()->begin(__FUNCTION__);

        try {
            $repository->delete($testId);
            $transaction->commit(__FUNCTION__);
        } catch (Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return true;
    }

    /**
     * Creates new test with scenario ID.
     *
     * @param int                          $scenarioId ID of scenario
     * @param \PF\Profiler\Repository\Test $repository Repository test instance
     * @param \PF\Main\Database            $database   Database instance
     *
     * @return \PF\Profiler\Entity\Test
     *
     * @throws \PF\Profiler\Service\Exception
     */
    public function createTest($scenarioId, Repository\Test $repository, Database $database) {
        $transaction = $database->getTransaction()->begin(__FUNCTION__);

        try {
            $test = $repository->create($scenarioId);
            $transaction->commit(__FUNCTION__);
        } catch (Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return $test;
    }

    /**
     * Updates state for test by actual states of assigned requests.
     *
     * @param int                          $testId         ID of test
     * @param \PF\Profiler\Repository\Test $repository     Repository test instance
     * @param \PF\Main\Database            $database       Database instance
     * @param \PF\Profiler\Service\Measure $measureService Measure service instance
     *
     * @return boolean
     * 
     * @throws \PF\Profiler\Service\Exception
     */
    public function updateTestState($testId, Repository\Test $repository, Database $database, Measure $measureService) {
        $transaction = $database->getTransaction()->begin(__FUNCTION__);

        $commander = $this->getExecutor(__FUNCTION__)->clean()->add('findMeasuresForTest', $measureService);
        $commander->getResult()->setTestId($testId);

        $measures   = $commander->execute()->getData();
        $states     = array();
        $finalState = null;

        foreach ($measures as $measure) {
            $states[$measure->getState()] = true;
        }

        if (isset($states[State::STATE_ERROR])) {
            $finalState = TestState::STATE_ERROR;
        } elseif (count($states) == 1) {
            if (array_key_exists(State::STATE_EMPTY, $states)) {
                $finalState = TestState::STATE_IDLE;
            } elseif (array_key_exists(State::STATE_STAT_GENERATED, $states)) {
                $finalState = TestState::STATE_DONE;
            } else {
                $finalState = TestState::STATE_MEASURE_ACTIVE;
            }
        } else {
            $finalState = TestState::STATE_MEASURE_ACTIVE;
        }

        try {
            $test = new Entity\Test(array('id' => $testId, 'state' => $finalState));
            $repository->update($testId, $test);
            $transaction->commit(__FUNCTION__);
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        return true;
    }
}
