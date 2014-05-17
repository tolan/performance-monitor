<?php

namespace PF\Profiler\Gearman;

use PF\Main\Http\Enum\ParameterType;
use PF\Profiler\Monitor\Enum\HttpKeys;
use PF\Profiler\Monitor\Enum\Type;

/**
 * This script defines profiler gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Worker extends \PF\Main\Abstracts\Gearman\Worker implements \PF\Main\Event\Interfaces\Sender {

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
        $testService     = $this->getProvider()->get('PF\Profiler\Service\Test'); /* @var $testService \PF\Profiler\Service\Test */
        $scenarioService = $this->getProvider()->get('PF\Profiler\Service\Scenario'); /* @var $commander \PF\Main\Commander\Executor */
        $commander       = $this->getProvider()->get('PF\Main\Commander\Executor')
            ->clean()
            ->add('getTest', $testService)
            ->add(function(\PF\Main\Commander\Result $result) {
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
            $this->_sendTest($test, $scenario);
            $commander->clean()->add('updateTestState', $testService)->execute();
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
        $client         = $this->getProvider()->prototype('PF\Main\Http\Client', true); /* @var $client \PF\Main\Http\Client */
        $measureService = $this->getProvider()->get('PF\Profiler\Service\Measure'); /* @var $measureService \PF\Profiler\Service\Measure */
        $commander      = $this->getProvider()->get('PF\Main\Commander\Executor')
            ->clean()
            ->add('createMySQLMeasure', $measureService);
        /* @var $commander \PF\Main\Commander\Executor */

        foreach ($scenario->getRequests() as $request) {
            $parameters = $request->get('parameters', array());
            foreach ($parameters as $key => $parameter) {
                $parameters[$key] = $parameter->toAraay();
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
     * @return \PF\Profiler\Gearman\Worker
     */
    public function send($messageData) {
        $message = $this->getProvider()->prototype('PF\Profiler\Gearman\EventMessage');
        $message->setData($messageData);
        $this->getProvider()->get('PF\Profiler\Event\Mediator')->send($message, $this);

        return $this;
    }
}
