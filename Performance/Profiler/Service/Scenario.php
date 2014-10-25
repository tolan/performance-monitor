<?php

namespace PM\Profiler\Service;

use PM\Main\Abstracts\Service;
use PM\Main\Database;
use PM\Profiler\Repository\Factory;
use PM\Profiler\Entity;

/**
 * This script defines class for scenario service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Scenario extends Service {

    /**
     * Returns list of all scenarios.
     *
     * @param \PM\Profiler\Repository\Factory $factory Repository factory instance
     *
     * @return array
     */
    public function findScenarios(Factory $factory) {
        return $factory->getScenario()->findScenarios();
    }

    /**
     * Creates new scenario with given data.
     *
     * @param array                           $scenarioData   Array with data for new scenario
     * @param \PM\Profiler\Repository\Factory $factory        Repository factory instance
     * @param \PM\Main\Database               $database       Database instance
     * @param \PM\Profiler\Service\Request    $requestService Request service instance
     *
     * @return \PM\Profiler\Entity\Scenario
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function createScenario($scenarioData, Factory $factory, Database $database, Request $requestService) {
        $transaction = $database->getTransaction()->begin('scenario');
        $repository  = $factory->getScenario(); /* @var $repository \PM\Profiler\Repository\Scenario */
        $scenario    = new Entity\Scenario($scenarioData);

        try {
            $repository->create($scenario);
            $transaction->commit('scenario');
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        $requests = array();
        $executor = $this->getExecutor(__FUNCTION__)->clean();
        $executor->getResult()->set('scenarioId', $scenario->getId());

        foreach ($scenario->get('requests', array()) as $requestData) {
            $executor->getResult()->set('requestData', $requestData);
            $requests[] = $executor->add('createRequest', $requestService)->execute()->getData();
            $executor->clean();
        }

        $scenario->set('requests', $requests);

        return $scenario;
    }

    /**
     * Updates existed scenario with new data.
     *
     * @param array                           $scenarioData   Array with data to update
     * @param \PM\Profiler\Repository\Factory $factory        Repository facctory instance
     * @param \PM\Main\Database               $database       Database instance
     * @param \PM\Profiler\Service\Request    $requestService Request service instance
     *
     * @return \PM\Profiler\Entity\Scenario
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function updateScenario($scenarioData, Factory $factory, Database $database, Request $requestService) {
        $repository  = $factory->getScenario();
        $transaction = $database->getTransaction()->begin(__FUNCTION__);
        $scenario    = $this->getScenario($scenarioData['id'], $factory, true, $requestService);

        try {
            $updateScenario = new Entity\Scenario($scenarioData);
            $repository->update($updateScenario);
            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        $this->_updateRequests($updateScenario, $scenario, $requestService, $database);

        return $this->getScenario($scenarioData['id'], $factory, true, $requestService);
    }

    /**
     * Deletes existed scenario by given ID.
     *
     * @param int                             $id       ID of scenario
     * @param \PM\Profiler\Repository\Factory $factory  Repository factory instance
     * @param \PM\Main\Database               $database Database instance
     *
     * @return boolean
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function deleteScenario($id, Factory $factory, Database $database) {
        $repositroy  = $factory->getScenario();
        $transaction = $database->getTransaction()->begin(__FUNCTION__);

        try {
            $repositroy->delete($id);
            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return true;
    }

    /**
     * Returns scenario by given ID.
     *
     * @param int                             $id              ID of scenario
     * @param \PM\Profiler\Repository\Factory $factory         Repository factory instance
     * @param boolean                         $includeElements Include elements assigned to requests (filters, parameters, ...)
     * @param \PM\Profiler\Service\Request    $requestService  Request service instance
     *
     * @return \PM\Profiler\Entity\Scenario
     */
    public function getScenario($id, Factory $factory, $includeElements = false, Request $requestService = null) {
        $repository = $factory->getScenario();
        $scenario   = $repository->getScenario($id);

        if ($includeElements === true) {
            $executor = $this->getExecutor(__FUNCTION__);
            $executor->getResult()->set('includeParams', true)
                ->set('includeFilters', true)
                ->set('scenarioId', $id);
            $requests = $executor->add('getRequestsForScenario', $requestService)->execute()->getData();
            $scenario->set('requests', $requests);
        }

        return $scenario;
    }

    /**
     * Updates requests assigned to scenario.
     *
     * @param \PM\Profiler\Entity\Scenario $updateScenario Scenario entity instance with data to update
     * @param \PM\Profiler\Entity\Scenario $scenario       Existed scenario entity instance
     * @param \PM\Profiler\Service\Request $requestService Request service instance
     * @param \PM\Main\Database            $database       Database instance
     *
     * @return \PM\Profiler\Service\Scenario
     * 
     * @throws \PM\Profiler\Service\Exception
     */
    private function _updateRequests(Entity\Scenario $updateScenario, Entity\Scenario $scenario, Request $requestService, Database $database) {
        $updateRequests  = $updateScenario->get('requests', array());
        $newRequests     = array();
        $existedRequests = array();

        foreach ($scenario->get('requests', array()) as $request) {
            $existedRequests[$request->getId()] = $request;
        }

        foreach ($updateRequests as $key => $request) {
            $request = new Entity\Request($request);
            $id = $request->get('id', 0);

            if (array_key_exists($id, $existedRequests)) {
                unset($existedRequests[$id]);
                $updateRequests[$key] = $request;
            } else {
                $newRequests[] = $request;
                unset($updateRequests[$key]);
            }
        }

        try {
            $executor = $this->getExecutor(__FUNCTION__)->clean();
            $executor->getResult()->set('scenarioId', $scenario->getId());
            foreach ($newRequests as $request) {
                $executor->getResult()->set('requestData', $request->toArray());
                $executor->add('createRequest', $requestService)->execute();
                $executor->clean();
            }

            foreach ($updateRequests as $request) {
                $executor->getResult()->set('requestData', $request->toArray());
                $executor->add('updateRequest', $requestService)->execute();
                $executor->clean();
            }

            foreach ($existedRequests as $request) {
                $executor->getResult()->set('id', $request->getId());
                $executor->add('deleteRequest', $requestService)->execute();
                $executor->clean();
            }
        } catch (Database\Exception $exc) {
            $database->getTransaction()->rollBack();
            throw $exc;
        }

        return $this;
    }
}
