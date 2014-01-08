<?php

namespace PF\Profiler\Gearman;

use PF\Profiler\Enum\HttpKeys;
use PF\Profiler\Enum\AttemptState;
use PF\Main\Http\Enum\ParameterType;

/**
 * This script defines profiler gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Worker extends \PF\Main\Abstracts\Gearman\Worker implements \PF\Main\Event\Interfaces\Sender {

    /**
     * Measure test data repository
     *
     * @var \PF\Profiler\Component\Repository\MeasureTest
     */
    private $_testRepository = null;

    /**
     * Measure data repository
     *
     * @var \PF\Profiler\Component\Repository\Measure
     */
    private $_measureRepository = null;

    /**
     * Attempt test repository.
     *
     * @var \PF\Profiler\Component\Repository\TestAttempt
     */
    private $_attemptRepository = null;

    /**
     * ID of measure.
     *
     * @var int
     */
    private $_measureId = null;

    /**
     * ID of test
     *
     * @var int
     */
    private $_testId = null;

    /**
     * Process method which execute measure, analyze and create statistic for attempt of measure.
     *
     * @return void
     */
    public function process() {
        $this->_measureRepository  = $this->getProvider()->get('PF\Profiler\Component\Repository\Measure');
        $this->_testRepository     = $this->getProvider()->get('PF\Profiler\Component\Repository\MeasureTest');
        $this->_attemptRepository  = $this->getProvider()->get('PF\Profiler\Component\Repository\TestAttempt');
        $messageData               = $this->getMessageData();
        $this->_measureId          = $messageData[HttpKeys::MEASURE_ID];
        $this->_testId             = $messageData[HttpKeys::TEST_ID];

        $this->_processMeasure();
        $this->_processAnalyze();
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
     * It provides measure part of whole process. It creates new attempt and make http GET request by measure settings.
     *
     * @return void
     */
    private function _processMeasure() {
        $measure = $this->_measureRepository->getMeasure($this->_measureId);

        try {
            $this->_testRepository->update($this->_testId, array('state' => AttemptState::STATE_MEASURE_ACTIVE));
            $this->_sendMeasure($measure);
            $this->_testRepository->update($this->_testId, array('state' => AttemptState::STATE_MEASURED));
        } catch (Exception $e) {
            $this->send($e);
            $this->_testRepository->update($this->_testId, array('state' => AttemptState::STATE_ERROR));
        }
    }

    /**
     * It provides analyze and statistics part of whole measure process. It takes measured call and transform it to analyzed tree and save statistics.
     *
     * @return void
     */
    private function _processAnalyze() {
        $attempts = $this->_attemptRepository->getAttempts($this->_testId);

        try {
            $this->_testRepository->update($this->_testId, array('state' => AttemptState::STATE_ANALYZE_ACTIVE));
            foreach ($attempts as $attempt) {
                // this is set for factories
                $this->getProvider()->get('request')->getGet()->set(HttpKeys::ATTEMPT_ID, $attempt['id']);
                $callStack = $this->getProvider()->get('PF\Profiler\Component\CallStack\Factory')
                    ->getCallStack(); /* @var $callStack \PF\Profiler\Component\CallStack\MySQL */
                $statictics = $this->getProvider()->get('PF\Profiler\Component\Statistics\Factory')
                    ->getStatistics(); /* @var $callStack \PF\Profiler\Component\Statistics\MySQL */

                $this->_attemptRepository->update($attempt['id'], array('state' => AttemptState::STATE_ANALYZE_ACTIVE));
                $callStack->reset();
                $callStack->setAttemptId($attempt['id']);
                $callStack->analyze();
                $this->_attemptRepository->update($attempt['id'], array('state' => AttemptState::STATE_ANALYZED));

                $this->_attemptRepository->update($attempt['id'], array('state' => AttemptState::STATE_STATISTIC_GENERATING));
                $statictics->reset();
                $statictics->setAttemptId($attempt['id']);
                $statictics->generate();
                $statictics->save();
                $this->_attemptRepository->update($attempt['id'], array('state' => AttemptState::STATE_STATISTIC_GENERATED));
            }

            $this->_testRepository->update($this->_testId, array('state' => AttemptState::STATE_STATISTIC_GENERATED));
        } catch (Exception $e) {
            $this->send($e);
            $this->_testRepository->update($this->_testId, array('state' => AttemptState::STATE_ERROR));
        }
    }

    /**
     * This method send requests of measure to test enviroment.
     *
     * @param array $measure Measure data
     *
     * @return void
     */
    private function _sendMeasure($measure) {
        $client = $this->getProvider()->prototype('PF\Main\Http\Client', true); /* @var $client \PF\Main\Http\Client */

        foreach ($measure['requests'] as $request) {
            $request['parameters'] = isset($request['parameters']) ? $request['parameters'] : array();
            if ($request['toMeasure'] == true) {
                $attemptId               = $this->_attemptRepository->create(
                    array(
                        'testId'     => $this->_testId,
                        'url'        => $request['url'],
                        'method'     => $request['method'],
                        'parameters' => $this->_getParameters($request),
                        'body'       => $this->_getBody($request)
                    )
                );
                $request['parameters'][] = array(
                    'method' => ParameterType::GET, 'name' => HttpKeys::PROFILER_START, 'value' => true
                );
                $request['parameters'][] = array(
                    'method' => ParameterType::GET, 'name' => HttpKeys::MEASURE_ID, 'value' => $measure['id']
                );
                $request['parameters'][] = array(
                    'method' => ParameterType::GET, 'name' => HttpKeys::TEST_ID, 'value' => $this->_testId
                );
                $request['parameters'][] = array(
                    'method' => ParameterType::GET, 'name' => HttpKeys::ATTEMPT_ID, 'value' => $attemptId
                );
            }

            $request = $client->createRequest($request['method'], $request['url'], $request['parameters']);
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
    private function _getParameters($request) {
        $result = array();
        if (isset($request['parameters'])) {
            foreach ($request['parameters'] as $parameter) {
                if ($parameter['method'] == ParameterType::GET) {
                    $result[] = $parameter['name'].' = '.$parameter['value'];
                }
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
    private function _getBody($request) {
        $result = array();
        if (isset($request['parameters'])) {
            foreach ($request['parameters'] as $parameter) {
                if ($parameter['method'] == ParameterType::POST) {
                    $result[] = $parameter['name'].' = '.$parameter['value'];
                }
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
