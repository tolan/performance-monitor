<?php

namespace PF\Main\Web\Controller;

use PF\Profiler\Enum;
use PF\Main\Http;

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

    /*
     * Measure repository
     *
     * @var \PF\Profiler\Component\Repository\Measure
     */
    private $_measureRepository = null;

    /**
     * Measure statistic data repository
     *
     * @var \PF\Profiler\Component\Repository\AttemptStatisticData
     */
    private $_statisticDataRepository = null;

    /**
     * Measure request data repository
     *
     * @var \PF\Profiler\Component\Repository\MeasureRequest
     */
    private $_requestRepository = null;

    /**
     * Measure request parameter data repository
     *
     * @var \PF\Profiler\Component\Repository\RequestParameter
     */
    private $_parameterRepository = null;

    /**
     * Measure test data repository
     *
     * @var \PF\Profiler\Component\Repository\MeasureTest
     */
    private $_testRepository = null;

    /**
     * Measure test data repository
     *
     * @var \PF\Profiler\Component\Repository\TestAttempt
     */
    private $_attemptRepository = null;

    /**
     * Init method sets repositories.
     *
     * @return void
     */
    public function init() {
        $this->_measureRepository   = $this->getProvider()->get('PF\Profiler\Component\Repository\Measure');
        $this->_requestRepository   = $this->getProvider()->get('PF\Profiler\Component\Repository\MeasureRequest');
        $this->_parameterRepository = $this->getProvider()->get('PF\Profiler\Component\Repository\RequestParameter');

        $this->_testRepository    = $this->getProvider()->get('PF\Profiler\Component\Repository\MeasureTest');
        $this->_attemptRepository = $this->getProvider()->get('PF\Profiler\Component\Repository\TestAttempt');

        $this->_statisticDataRepository = $this->getProvider()->get('PF\Profiler\Component\Repository\AttemptStatisticData');
    }

    /**
     * This find all measures.
     *
     * @link /measures
     *
     * @method GET
     *
     * @return void
     */
    public function actionMeasures() {
        $this->setData(
            array_values($this->_measureRepository->getMeasures())
        );
    }

    /**
     * Gets measure by id.
     *
     * @link /measure/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionMeasure($params) {
        $data = $this->_measureRepository->getMeasure($params['id']);

        $this->setData($data);
    }

    /**
     * Method for delete measure by id.
     *
     * @link /measure/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDelete($params) {
        $this->_measureRepository->delete($params['id']);
        $this->setData(true);
    }

    /**
     * Method for create new measure.
     *
     * @link /measure
     *
     * @method POST
     *
     * @return void
     */
    public function actionCreate() {
        $input = $this->getRequest()->getInput();

        $measureId = $this->_measureRepository->create(array(
            'name'        => $input['name'],
            'description' => $input['description']
        ));

        foreach ($input['requests'] as $request) {
            $this->_createRequest($measureId, $request);
        }

        $this->setData($measureId);
    }

    /**
     * Creates new request for measure with given request data.
     *
     * @param int   $measureId ID of measure
     * @param array $request   Request data (url, method, toMeasure, parameters)
     *
     * @return \PF\Main\Web\Controller\Profiler
     */
    private function _createRequest($measureId, $request) {
        $requestId = $this->_requestRepository->create(array(
            'measureId' => $measureId,
            'url'       => $request['url'],
            'method'    => $request['method'],
            'toMeasure' => $request['toMeasure']
        ));

        if (isset($request['parameters']) && !empty($request['parameters'])) {
            foreach ($request['parameters'] as &$parameter) {
                $parameter['requestId'] = $requestId;
            }

            $this->_parameterRepository->massCreate($request['parameters']);
        }

        return $this;
    }

    /**
     * Method for update measure by id.
     *
     * @link /measure/{id}
     *
     * @method PUT
     *
     * @return void
     */
    public function actionUpdate($params) {
        $id    = (int)$params['id'];
        $input = $this->getRequest()->getInput();

        $this->_measureRepository->update($id, array(
            'name'        => $input['name'],
            'description' => $input['description']
        ));

        $measure = $this->_measureRepository->getMeasure($id);

        foreach ($measure['requests'] as $request) {
            $this->_requestRepository->delete($request['id']);
        }

        foreach ($input['requests'] as $request) {
            $this->_createRequest($id, $request);
        }

        $this->setData(true);
    }

    /**
     * Find all tests for measure by measure id.
     *
     * @link /measure/{measureId}/tests
     *
     * @method GET
     *
     * @return void
     */
    public function actionTests($params) {
        $data = $this->_testRepository->getTests($params['measureId']);

        $this->setData($data);
    }

    /**
     * It launch test for measure by given measure id.
     *
     * @link /measure/{measureId}/test/start
     *
     * @method POST
     *
     * @param array $params Input parameters
     *
     * @return void
     */
    public function actionStartTest($params) {
        $testId = $this->_testRepository->create(
            array('measureId' => $params['measureId'])
        );

        $this->getProvider()
            ->get('PF\Profiler\Gearman\Client')
            ->setData(
                array(
                    Enum\HttpKeys::MEASURE_ID => $params['measureId'],
                    Enum\HttpKeys::TEST_ID    => $testId
                )
            )
            ->doAsynchronize();

        $this->setData(true);
    }

    /**
     * Get test by given id.
     *
     * @link /measure/test/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionTest($params) {
        $data = $this->_testRepository->getTest($params['id']);

        $this->setData(current($data));
    }

    /**
     * Delete test by given id.
     *
     * @link /measure/test/{id}
     *
     * @method DELETE
     *
     * @param array $params Input parameters
     *
     * @return void
     */
    public function actionDeleteTest($params) {
        $this->_testRepository->delete($params['id']);

        $this->setData(true);
    }

    /**
     * Find all attempts for test by test id.
     *
     * @link /measure/test/{testId}/attempts
     *
     * @method GET
     *
     * @param type $params
     */
    public function actionGetAttempts($params) {
        $data = $this->_attemptRepository->getAttempts($params['testId']);

        $this->setData($data);
    }

    /**
     * Gets statistics for attempt by attempt id.
     *
     * @link /test/attempt/{id}/statistic
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetAttemptStatistic($params) {
        if ($params['id'] === 'browser') {
            $facade = $this->getProvider()->get('PF\Profiler\Main\Factory\Facade')->getFacade(); /* @var $facade \PF\Profiler\Main\Facade */
            $stats = $facade->getStatistics();
            $data = array();
        } else {
            $data = $this->_testRepository->getAttemptStatistic($params['id']);
        }

        $this->setData($data);
    }

    /**
     * Gets calls stack for attempt by attempt id and parent call id (zero means all root calls)
     *
     * @link /test/attempt/{id}/callStack/parent/{parentId}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetAttemptCallStack($params) {
        $data = $this->_statisticDataRepository->getAttemptCallStack($params['id'], $params['parentId']);

        $this->setData($data);
    }

    /**
     * Gets function statistics for attempt by attempt id.
     *
     * @link /test/attempt/{id}/statistic/function
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetAttemptFunctionStatistic($params) {
        $data = $this->_statisticDataRepository->getAttemptFunctionStatistic($params['id']);

        $this->setData($data);
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
        $methods = Http\Enum\Method::getConstants();

        $result = array();
        foreach ($methods as $method) {
            $result['requests'][] = array(
                'value' => $method,
                'name'  => 'profiler.measure.request.method.'.strtolower($method)
            );
        }

        $paramMethods = Http\Enum\ParameterType::getAllowedParams();
        foreach ($paramMethods as $method => $allowed) {
            foreach ($allowed as $allow) {
                $result['params'][$method][] = array(
                    'value' => $allow,
                    'name'  => 'profiler.measure.request.method.'.strtolower($allow)
                );
            }
        }

        $this->setData($result);
    }
}
