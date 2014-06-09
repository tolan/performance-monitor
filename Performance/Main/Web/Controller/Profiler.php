<?php

namespace PF\Main\Web\Controller;

use PF\Main\Http;
use PF\Profiler\Monitor;
use PF\Profiler\Gearman\Client;
use PF\Profiler\Enum\HttpKeys;

use PF\Main\Web\Component\Request;
use PF\Main\Database;

/**
 * This scripts defines class for profiler controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @link /profiler
 */
class Profiler extends Abstracts\Json {

    /**
     * This find all scenarios in database.
     *
     * @link /mysql/scenarios
     *
     * @method GET
     *
     * @return void
     */
    public function actionFindMySQLScenarions() {
        $scenarioService = $this->getProvider()->get('\PF\Profiler\Service\Scenario');

        $this->getExecutor()
            ->add('findScenarios', $scenarioService);
    }

    /**
     * Method for create new MySQL scenario.
     *
     * @link /mysql/scenario
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreateMySQLScenario() {
        $scenarioService = $this->getProvider()->get('\PF\Profiler\Service\Scenario');

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
     * @link /mysql/scenario/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdateMySQLScenario() {
        $scenarioService = $this->getProvider()->get('\PF\Profiler\Service\Scenario');
        /* @var $scenarioService \PF\Profiler\Service\Scenario */

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
     * @link /mysql/scenario/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteMySQLScenario($id) {
        $scenarioService = $this->getProvider()->get('\PF\Profiler\Service\Scenario');

        $this->getExecutor()
        ->add(function(Database $database) {
            $database->getTransaction()->begin();
        })
        ->add('deleteScenario', $scenarioService)
        ->add(function(Database $databasse, $data) {
            $databasse->getTransaction()->commitAll();
            return $data;
        })->getResult()->setId($id);
    }

    /**
     * This get scenario by given id.
     *
     * @link /mysql/scenario/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetMySQLScenario($id) {
        $scenarioService = $this->getProvider()->get('\PF\Profiler\Service\Scenario');
        /* @var $scenarioService \PF\Profiler\Service\Scenario */

        $this->getExecutor()
            ->add('getScenario', $scenarioService)
            ->getResult()
            ->set('includeElements', true)
            ->setId($id);
    }

    /**
     * Find all tests for MySQL scenario by scenario id.
     *
     * @link /mysql/scenario/{scenarioId}/tests
     *
     * @method GET
     *
     * @param int $scenarioId Scenario ID
     *
     * @return void
     */
    public function actionMySQLScenarioTests($scenarioId) {
        $testService = $this->getProvider()->get('\PF\Profiler\Service\Test');
        /* @var $testService \PF\Profiler\Service\Test */

        $this->getExecutor()
            ->add('getTestsForScenario', $testService)
            ->getResult()
            ->setScenarioId($scenarioId);
    }

    /**
     * Delete test of MySQL scenario by given id.
     *
     * @link /mysql/scenario/test/{testId}
     *
     * @method DELETE
     *
     * @param int $testId Test ID of MySQL scenario
     *
     * @return void
     */
    public function actionMySQLScenarioDeleteTest($testId) {
        $testService = $this->getProvider()->get('\PF\Profiler\Service\Test');
        /* @var $testService \PF\Profiler\Service\Test */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('deleteTest', $testService)
            ->add(function(Database $database, $data) {
                $database->getTransaction()->commitAll();
                return $data;
            })
            ->getResult()
            ->setTestId($testId);
    }

    /**
     * Get test by given id.
     *
     * @link /mysql/scenario/test/{testId}
     *
     * @method GET
     *
     * @param int $testId Test ID of MySQL scenario
     *
     * @return void
     */
    public function actionGetTest($testId) {
        $testService = $this->getProvider()->get('\PF\Profiler\Service\Test');
        /* @var $testService \PF\Profiler\Service\Test */

        $this->getExecutor()
            ->add('getTest', $testService)
            ->getResult()
            ->setTestId($testId);
    }

    /**
     * It launch test for scenario by given scenario id.
     *
     * @link /mysql/scenario/{scenarioId}/test/start
     *
     * @method POST
     *
     * @param int $scenarioId Scenario ID
     *
     * @return void
     */
    public function actionMySQLScenarioStartTest($scenarioId) {
        $testService = $this->getProvider()->get('\PF\Profiler\Service\Test');
        /* @var $testService \PF\Profiler\Service\Test */

        $this->getExecutor()
            ->add(function(Database $database) {
                $database->getTransaction()->begin();
            })
            ->add('createTest', $testService)
            ->add(function(Database $database) {
                $database->getTransaction()->commitAll();
            })
            ->add(function(Client $client, $data) {
                $client->setData(array(HttpKeys::TEST_ID => $data->getId()))
                    ->doAsynchronize();
            })
            ->getResult()
            ->setScenarioId($scenarioId);
    }

    /**
     * This find all measures.
     *
     * @link /mysql/scenario/test/{testId}/measures
     *
     * @method GET
     *
     * @param int $testId Test ID of MySQL scenario
     *
     * @return void
     */
    public function actionMySQLTestMeasures($testId) {
        $measureService = $this->getProvider()->get('\PF\Profiler\Service\Measure');
        /* @var $measureService \PF\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('findMeasuresForTest', $measureService)
            ->getResult()
            ->setTestId($testId);
    }

    /**
     * Gets statistics for measure by measure id.
     *
     * @link /{type}/measure/{id}/summary
     *
     * @method GET
     *
     * @session_write_close false
     *
     * @param enum $type One of enum \PF\Profiler\Monitor\Enum\Type
     * @param int  $id   Measure ID
     *
     * @return void
     */
    public function actionGetMeasureStatistic($type, $id) {
        $measureService = $this->getProvider()->get('\PF\Profiler\Service\Measure');
        /* @var $measureSerice \PF\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('getMeasureStatistics', $measureService)
            ->getResult()
            ->setType($type)
            ->setMeasureId($id);

        return $this->getExecutor()->execute()
            ->reset('input')
            ->reset('type')
            ->reset('measureId')
            ->toArray();
    }

    /**
     * Gets calls stack for measure by measure id and parent call id (zero means all root calls)
     *
     * @link /{type}/measure/{id}/callStack/parent/{parentId}
     *
     * @method GET
     *
     * @session_write_close false
     *
     * @param enum $type     One of enum \PF\Profiler\Monitor\Enum\Type
     * @param int  $id       Measure ID
     * @param int  $parentId ID of parent call
     *
     * @return void
     */
    public function actionGetMeasureCallStack($type, $id, $parentId) {
        $measureService = $this->getProvider()->get('\PF\Profiler\Service\Measure');
        /* @var $measureSerice \PF\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('getMeasureCallStack', $measureService)
            ->getResult()
            ->setType($type)
            ->setMeasureId($id)
            ->setParentId($parentId);
    }

    /**
     * Gets function statistics for measure by measure id.
     *
     * @link /{type}/measure/{id}/statistic/function
     *
     * @method GET
     * 
     * @session_write_close false
     *
     * @param enum $type     One of enum \PF\Profiler\Monitor\Enum\Type
     * @param int  $id       Measure ID
     *
     * @return void
     */
    public function actionGetMeasureCallsStatistic($type, $id) {
        $measureService = $this->getProvider()->get('\PF\Profiler\Service\Measure');
        /* @var $measureSerice \PF\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('getMeasureCallsStatistic', $measureService)
            ->getResult()
            ->setType($type)
            ->setMeasureId($id);
    }

    /**
     * Returns list of measures stored in files.
     *
     * @link /file/measures
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetFileMeasures() {
        $measureService = $this->getProvider()->get('\PF\Profiler\Service\Measure');
        /* @var $measureSerice \PF\Profiler\Service\Measure */

        $this->getExecutor()->add('findFileMeasures', $measureService);
    }

    /**
     * Delete action of file with measure.
     *
     * @link /file/measure/{id}
     *
     * @method DELETE
     *
     * @param string $id Measure identification
     *
     * @return void
     */
    public function actionDeleteFileMeasure($id) {
        $measureService = $this->getProvider()->get('\PF\Profiler\Service\Measure');
        /* @var $measureSerice \PF\Profiler\Service\Measure */

        $this->getExecutor()
            ->add('deleteFileMeasure', $measureService)
            ->getResult()
            ->setMeasureId($id);
    }

    /**
     * Gets information about request and their parameters methods.
     *
     * @link /request/methods
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetMethods() {
        $methods = Http\Enum\Method::getSelection('profiler.scenario.request.method.');

        $result = array(
            'requests' => $methods
        );

        $paramMethods = Http\Enum\ParameterType::getAllowedParams();
        foreach ($paramMethods as $method => $allowed) {
            foreach ($allowed as $allow) {
                $result['params'][$method][] = array(
                    'value' => $allow,
                    'name'  => 'profiler.scenario.request.method.'.$allow
                );
            }
        }

        $this->setData($result);
    }

    /**
     * Gets options for filters
     *
     * @link /filter/options
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetFilterOptions() {
        $this->getExecutor()->add(function($data = array()) {
            $data['types'] = Monitor\Filter\Enum\Type::getSelection('profiler.scenario.request.filter.type.');

            return array('data' => $data);
        })->add(function($data = array()) {
            $data['params'] = Monitor\Filter\Association::getAssociation();

            return array('data' => $data);
        });
    }
}
