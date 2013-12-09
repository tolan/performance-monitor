<?php

/**
 * This script defines profiler gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Gearman_Worker extends Performance_Main_Abstract_Gearman_Worker {

    /**
     * Process method which execute measure, analyze and create statistic for attempt of measure.
     *
     * @return void
     */
    public function process() {
        $this->_processMeasure();
        $this->_processAnalyze();
        $this->_processStatistic();
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
        $repositoryMeasure  = $this->getProvider()->get('Performance_Profiler_Component_Repository_Measure');
        $repositoryAttempt  = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureAttempt');

        $messageData = $this->getMessageData();
        $measureId   = $messageData['id'];
        $measure     = $repositoryMeasure->getMeasure($measureId);

        $attemptId = $repositoryAttempt->create(
            array(
                'profiler_measure_id' => $measureId,
                'state'               => Performance_Profiler_Enum_AttemptState::STATE_MEASURE_ACTIVE,
                'started'             => time()
            )
        );
        $this->getMessage()->setData(array(Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID => $attemptId));
        $this->getProvider()->get('Performance_Main_Web_Component_Request')->getGet()->set(Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID, $attemptId);
        $address = $this->_formatAddress($measure, $attemptId);

        try {
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_MEASURE_ACTIVE));
            file_get_contents($address);
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_MEASURED));
        } catch (Exception $e) {
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ERROR));
        }
    }

    /**
     * It provides analyze part of whole measure process. It takes measured call and transform it to analyzed tree.
     *
     * @return void
     */
    private function _processAnalyze() {
        $messageData       = $this->getMessageData();
        $callStack         = $this->getProvider()->get('Performance_Profiler_Component_CallStack_Factory')->getCallStack();
        $repositoryAttempt = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureAttempt');
        $attemptId         = $messageData[Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID];

        try {
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ANALYZE_ACTIVE));
            $callStack->reset();
            $callStack->setAttemptId($attemptId);
            $callStack->analyze();
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ANALYZED));
        } catch (Exception $e) {
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ERROR));
        }
    }

    /**
     * It provides statistic part of whole measure process. It takes analyzed tree from before step a make some statistic for each call.
     *
     * @return void
     */
    private function _processStatistic() {
        $messageData       = $this->getMessageData();
        $statictics        = $this->getProvider()->get('Performance_Profiler_Component_Statistics_Factory')->getStatistics();
        $repositoryAttempt = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureAttempt');
        $attemptId         = $messageData[Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID];

        try {
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_STATISTIC_GENERATING));
            $statictics->reset();
            $statictics->setAttemptId($attemptId);
            $statictics->generate();
            $statictics->save();
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_STATISTIC_GENERATED));
        } catch (Exception $e) {
            $repositoryAttempt->update($attemptId, array('state' => Performance_Profiler_Enum_AttemptState::STATE_ERROR));
        }
    }

    /**
     * It creates link for measure and attempt.
     * Example: http://perf.lc?PROFILER_ENABLE=TRUE
     *
     * @param array $measure   Array with basic information of measure
     * @param int   $attemptId ID of attempt
     *
     * @return string
     */
    private function _formatAddress($measure, $attemptId) {
        $address = (preg_match('#^https*://#', $measure['link']) ? '' : 'http://') . $measure['link'];
        $parameters = array();

        foreach ($measure['parameters'] as $param) {
            if (!empty($param['key']) && !empty($param['value'])) {
                $parameters[] =  $param['key'].'='.$param['value'];
            }
        }

        $parameters[] = Performance_Profiler_Enum_HttpKeys::PROFILER_START .'=TRUE';
        $parameters[] = Performance_Profiler_Enum_HttpKeys::MEASURE_ID.'='.$measure['id'];
        $parameters[] = Performance_Profiler_Enum_HttpKeys::ATTEMPT_ID.'='.$attemptId;

        return $address . (empty($parameters) ? '' : ('?'.join('&', $parameters)));
    }
}
