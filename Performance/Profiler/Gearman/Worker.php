<?php

/**
 * This script defines profiler gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Gearman_Worker extends Performance_Main_Abstract_Gearman_Worker implements Performance_Main_Event_Interface_Sender {

    /**
     * Measure test data repository
     *
     * @var Performance_Profiler_Component_Repository_MeasureTest
     */
    private $_testRepository = null;

    /**
     * Measure data repository
     *
     * @var Performance_Profiler_Component_Repository_Measure
     */
    private $_measureRepository = null;

    /**
     *
     * @var Performance_Profiler_Component_Repository_TestAttempt
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
        $this->_measureRepository  = $this->getProvider()->get('Performance_Profiler_Component_Repository_Measure');
        $this->_testRepository     = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureTest');
        $this->_attemptRepository  = $this->getProvider()->get('Performance_Profiler_Component_Repository_TestAttempt');
        $messageData               = $this->getMessageData();
        $this->_measureId          = $messageData[Performance_Profiler_Enum_HttpKeys::MEASURE_ID];
        $this->_testId             = $messageData[Performance_Profiler_Enum_HttpKeys::TEST_ID];

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
            $this->_testRepository->update($this->_testId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_MEASURE_ACTIVE));
            $this->_sendMeasure($measure);
            $this->_testRepository->update($this->_testId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_MEASURED));
        } catch (Exception $e) {
            $this->send($e);
            $this->_testRepository->update($this->_testId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ERROR));
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
            $this->_testRepository->update($this->_testId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ANALYZE_ACTIVE));
            foreach ($attempts as $attempt) {
                // this is set for factories
                $this->getProvider()->get('request')->getGet()->set(Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID, $attempt['id']);
                $callStack = $this->getProvider()->get('Performance_Profiler_Component_CallStack_Factory')
                    ->getCallStack(); /* @var $callStack Performance_Profiler_Component_CallStack_MySQL */
                $statictics = $this->getProvider()->get('Performance_Profiler_Component_Statistics_Factory')
                    ->getStatistics(); /* @var $callStack Performance_Profiler_Component_Statistics_MySQL */

                $this->_attemptRepository->update($attempt['id'], array('state' => Performance_Profiler_Enum_AttemptState::STATE_ANALYZE_ACTIVE));
                $callStack->reset();
                $callStack->setAttemptId($attempt['id']);
                $callStack->analyze();
                $this->_attemptRepository->update($attempt['id'], array('state' => Performance_Profiler_Enum_AttemptState::STATE_ANALYZED));

                $this->_attemptRepository->update($attempt['id'], array('state' => Performance_Profiler_Enum_AttemptState::STATE_STATISTIC_GENERATING));
                $statictics->reset();
                $statictics->setAttemptId($attempt['id']);
                $statictics->generate();
                $statictics->save();
                $this->_attemptRepository->update($attempt['id'], array('state' => Performance_Profiler_Enum_AttemptState::STATE_STATISTIC_GENERATED));
            }

            $this->_testRepository->update($this->_testId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_STATISTIC_GENERATED));
        } catch (Exception $e) {
            $this->send($e);
            $this->_testRepository->update($this->_testId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ERROR));
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
        $client        = $this->getProvider()->prototype('Performance_Main_Http_Client', true); /* @var $client Performance_Main_Http_Client */
        $parameterEnum = 'Performance_Main_Http_Enum_ParameterType';

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
                    'method' => $parameterEnum::GET, 'name' => Performance_Profiler_Enum_HttpKeys::PROFILER_START, 'value' => true
                );
                $request['parameters'][] = array(
                    'method' => $parameterEnum::GET, 'name' => Performance_Profiler_Enum_HttpKeys::MEASURE_ID, 'value' => $measure['id']
                );
                $request['parameters'][] = array(
                    'method' => $parameterEnum::GET, 'name' => Performance_Profiler_Enum_HttpKeys::TEST_ID, 'value' => $this->_testId
                );
                $request['parameters'][] = array(
                    'method' => $parameterEnum::GET, 'name' => Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID, 'value' => $attemptId
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
                if ($parameter['method'] == Performance_Main_Http_Enum_ParameterType::GET) {
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
                if ($parameter['method'] == Performance_Main_Http_Enum_ParameterType::POST) {
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
     * @return Performance_Profiler_Gearman_Worker
     */
    public function send($messageData) {
        $message = new Performance_Profiler_Gearman_EventMessage();
        $message->setData($messageData);
        $this->getProvider('Performance_Profiler_Event_Mediator')->send($message, $this);

        return $this;
    }
}
