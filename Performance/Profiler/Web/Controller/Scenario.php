<?php

namespace PM\Profiler\Web\Controller;

use PM\Main\Web\Controller\Abstracts\Json;
use PM\Main\Web\Component\Request;
use PM\Main\Database;
use PM\Profiler\Gearman\Client;
use PM\Profiler\Enum\HttpKeys;

/**
 * This scripts defines class of profiler scenario controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 *
 * @link /profiler/scenario
 */
class Scenario extends Json {

    /**
     * This find all scenarios in database.
     *
     * @link /mysql/get
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindMySQLScenarions() {
        $scenarioService = $this->getProvider()->get('\PM\Profiler\Service\Scenario'); /* @var \PM\Profiler\Service\Scenario */

        $this->getExecutor()
            ->add('findScenarios', $scenarioService);
    }

    /**
     * This get scenario by given id.
     *
     * @link /mysql/get/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetMySQLScenario($id) {
        $scenarioService = $this->getProvider()->get('\PM\Profiler\Service\Scenario');
        /* @var $scenarioService \PM\Profiler\Service\Scenario */

        $this->getExecutor()
            ->add('getScenario', $scenarioService, array('id' => $id, 'includeElements' => true));
    }

    /**
     * Method for create new MySQL scenario.
     *
     * @link /mysql/create
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateMySQLScenario() {
        $scenarioService = $this->getProvider()->get('\PM\Profiler\Service\Scenario');

        $this->getExecutor()
        ->add(function(Database $database, Request $request) {
            $database->getTransaction()->begin();
            return array('scenarioData' => $request->getInput());
        })
        ->add('createScenario', $scenarioService)
        ->add(function(Database $databasse) {
            $databasse->getTransaction()->commitAll();
        });
    }

    /**
     * Method for update MySQL scenario.
     *
     * @link /mysql/update/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateMySQLScenario() {
        $scenarioService = $this->getProvider()->get('\PM\Profiler\Service\Scenario');
        /* @var $scenarioService \PM\Profiler\Service\Scenario */

        $this->getExecutor()
        ->add(function(Database $database, $input) {
            $database->getTransaction()->begin();
            return array('scenarioData' => $input);
        })
        ->add('updateScenario', $scenarioService)
        ->add(function(Database $databasse) {
            $databasse->getTransaction()->commitAll();
        });
    }

    /**
     * Method for delete MySQL scenario by id.
     *
     * @link /mysql/delete/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteMySQLScenario($id) {
        $scenarioService = $this->getProvider()->get('\PM\Profiler\Service\Scenario');

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteScenario', $scenarioService, array('id' => $id))
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        });
    }

    /**
     * Find all tests for MySQL scenario by scenario id.
     *
     * @link /mysql/{scenarioId}/test/get
     *
     * @method GET
     *
     * @param int $scenarioId Scenario ID
     *
     * @return void
     */
    public function actionMySQLScenarioTests($scenarioId) {
        $testService = $this->getProvider()->get('\PM\Profiler\Service\Test');
        /* @var $testService \PM\Profiler\Service\Test */

        $this->getExecutor()
            ->add('getTestsForScenario', $testService, array('scenarioId' => $scenarioId));
    }

    /**
     * It launch test for scenario by given scenario id.
     *
     * @link /mysql/{scenarioId}/test/start
     *
     * @method POST
     *
     * @param int $scenarioId Scenario ID
     *
     * @return void
     */
    public function actionMySQLScenarioStartTest($scenarioId) {
        $testService = $this->getProvider()->get('\PM\Profiler\Service\Test');
        /* @var $testService \PM\Profiler\Service\Test */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('createTest', $testService, array('scenarioId' => $scenarioId))
            ->add(function(Database $database) {
                $database->getTransaction()->commitAll();
            })
            ->add(function(Client $client, $data) {
                $client->setData(array(HttpKeys::TEST_ID => $data->getId()))
                    ->doAsynchronize();
            });
    }
}
