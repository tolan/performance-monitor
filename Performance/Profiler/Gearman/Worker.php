<?php

namespace PM\Profiler\Gearman;

use PM\Main\Http\Enum\ParameterType;
use PM\Profiler\Monitor\Enum\HttpKeys;
use PM\Profiler\Monitor\Enum\Type;

/**
 * This script defines profiler gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Worker extends \PM\Main\Abstracts\Gearman\Worker implements \PM\Main\Event\Interfaces\Sender {

    /**
     * Test ID of MySQL scenario
     *
     * @var int
     */
    private $_testId = null;

    /**
     * Process method which execute test of scenario.
     *
     * @return void
     */
    public function process() {
        $messageData   = $this->getMessageData();
        $this->_testId = $messageData[HttpKeys::TEST_ID];

        $this->_processTest();
    }

    /**
     * Result for asynchronous job is true.
     *
     * @return boolean
     */
    public function getResult() {
        return true;
    }

    /**
     * This method process test. It loads test and send it.
     *
     * @return void
     */
    private function _processTest() {
        $testService     = $this->getProvider()->get('PM\Profiler\Service\Test'); /* @var $testService \PM\Profiler\Service\Test */
        $scenarioService = $this->getProvider()->get('PM\Profiler\Service\Scenario'); /* @var $commander \PM\Main\Commander\Executor */
        $commander       = $this->getProvider()->get('PM\Main\Commander\Executor')
            ->clean()
            ->add('getTest', $testService)
            ->add(function(\PM\Main\Commander\Result $result) {
                $test = $result->getData();
                $result->setTest($test)
                    ->setId($test->getScenarioId())
                    ->setIncludeElements(true);
            })
            ->add('getScenario', $scenarioService);
        $commander->getResult()->setTestId($this->_testId);

        $result   = $commander->execute();
        $test     = $result->getTest();
        $scenario = $result->getData();

        try {
            $commander->clean()->add('updateTestState', $testService)->execute();
            $this->_sendTest($test, $scenario);
        } catch (Exception $e) {
            $commander->clean()->add('updateTestState', $testService)->execute();
            $this->send($e);
        }
    }

    /**
     * This method send requests of test to monitored enviroment.
     *
     * @param array $measure Measure data
     *
     * @return void
     */
    private function _sendTest($test, $scenario) {
        $client         = $this->getProvider()->prototype('PM\Main\Http\Client', true); /* @var $client \PM\Main\Http\Client */
        $measureService = $this->getProvider()->get('PM\Profiler\Service\Measure'); /* @var $measureService \PM\Profiler\Service\Measure */
        $commander      = $this->getProvider()->get('PM\Main\Commander\Executor')
            ->clean()
            ->add('createMySQLMeasure', $measureService);
        /* @var $commander \PM\Main\Commander\Executor */

        foreach ($scenario->getRequests() as $request) {
            $parameters = $request->get('parameters', array());
            foreach ($parameters as $key => $parameter) {
                $parameters[$key] = $parameter->toArray();
            }

            if ($request->getToMeasure() == true) {
                $commander->getResult()->setMeasureData(
                    array(
                        'testId'     => $test->getId(),
                        'url'        => $request->get('url'),
                        'method'     => $request->get('method'),
                        'parameters' => $this->_getParametersString($request),
                        'body'       => $this->_getBodyString($request)
                    )
                );

                $measureId = $commander->execute()->getData()->getId();

                $parameters[] = array('method' => ParameterType::GET, 'name' => HttpKeys::PROFILER_START, 'value' => true);
                $parameters[] = array('method' => ParameterType::GET, 'name' => HttpKeys::TYPE,           'value' => Type::MYSQL);
                $parameters[] = array('method' => ParameterType::GET, 'name' => HttpKeys::MEASURE_ID,     'value' => $measureId);
            }

            $request = $client->createRequest($request->getMethod(), $request->getUrl(), $parameters);
            $client->addRequest($request);
        }

        $client->send();
    }

    /**
     * Extract get parameters from request and returns formated string.
     *
     * @param array $request Request data
     *
     * @return string
     */
    private function _getParametersString($request) {
        $result = array();
        foreach ($request->get('parameters', array()) as $parameter) {
            if ($parameter->get('method') == ParameterType::GET) {
                $result[] = $parameter->get('name').' = '.$parameter->get('value');
            }
        }

        return join(', ', $result);
    }

    /**
     * Extract post parameters from request and returns formated string.
     *
     * @param array $request Request data
     *
     * @return string
     */
    private function _getBodyString($request) {
        $result = array();
        foreach ($request->get('parameters', array()) as $parameter) {
            if ($parameter->get('method') == ParameterType::POST) {
                $result[] = $parameter->get('name').' = '.$parameter->get('value');
            }
        }

        return join(', ', $result);
    }

    /**
     * It sends message to mediator.
     *
     * @param mixed $messageData Message data
     *
     * @return \PM\Profiler\Gearman\Worker
     */
    public function send($messageData) {
        $message = $this->getProvider()->prototype('PM\Profiler\Gearman\EventMessage');
        $message->setData($messageData);
        $this->getProvider()->get('PM\Profiler\Event\Mediator')->send($message, $this);

        return $this;
    }
}
