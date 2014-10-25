<?php

namespace PM\Profiler\Service;

use PM\Main\Abstracts\Service;
use PM\Main\Database;
use PM\Profiler\Repository;
use PM\Profiler\Enum\TestState;
use PM\Profiler\Monitor\Storage\State;
use PM\Profiler\Entity;

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
     * @param \PM\Profiler\Repository\Test $repository Repository test instance
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
     * @param \PM\Profiler\Repository\Test $repository Repository test instance
     *
     * @return \PM\Profiler\Entity\Test
     */
    public function getTest($testId, Repository\Test $repository) {
        return $repository->getTest($testId);
    }

    /**
     * Deletes test by given ID.
     *
     * @param int                          $testId     ID of test
     * @param \PM\Profiler\Repository\Test $repository Repository test instance
     * @param \PM\Main\Database            $database   Database instance
     *
     * @return boolean
     *
     * @throws \PM\Profiler\Service\Exception
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
     * @param \PM\Profiler\Repository\Test $repository Repository test instance
     * @param \PM\Main\Database            $database   Database instance
     *
     * @return \PM\Profiler\Entity\Test
     *
     * @throws \PM\Profiler\Service\Exception
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
     * @param \PM\Profiler\Repository\Test $repository     Repository test instance
     * @param \PM\Main\Database            $database       Database instance
     * @param \PM\Profiler\Service\Measure $measureService Measure service instance
     *
     * @return boolean
     * 
     * @throws \PM\Profiler\Service\Exception
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
