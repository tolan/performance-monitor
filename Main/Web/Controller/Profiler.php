<?php

/**
 * This scripts defines class for profiler controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @link /profiler
 */
class Performance_Main_Web_Controller_Profiler extends Performance_Main_Web_Controller_Abstract_Json {

    /*
     * Measure repository
     *
     * @var Performance_Profiler_Component_Repository_Measure
     */
    private $_measureRepository = null;

    /*
     * Measure attempt repository
     *
     * @var Performance_Profiler_Component_Repository_MeasureAttempt
     */
    private $_attemptRepository = null;

    /**
     * Measure statistic repository
     *
     * @var Performance_Profiler_Component_Repository_MeasureStatistic
     */
    private $_statisticRepository = null;

    /**
     * Measure statistic data repository
     *
     * @var Performance_Profiler_Component_Repository_MeasureStatisticData
     */
    private $_statisticDataRepository = null;

    /**
     * Init method sets repositories.
     *
     * @return void
     */
    public function init() {
        $this->_measureRepository       = $this->getProvider()->get('Performance_Profiler_Component_Repository_Measure');
        $this->_attemptRepository       = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureAttempt');
        $this->_statisticRepository     = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureStatistic');
        $this->_statisticDataRepository = $this->getProvider()->get('Performance_Profiler_Component_Repository_MeasureStatisticData');
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
        $data = $this->_measureRepository->getMeasures(array($params['id']));

        $this->setData($data[$params['id']]);
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
        $input           = $this->getRequest()->getInput();
        $params          = $input['parameters'];
        $input['edited'] = Performance_Main_Database::convertTimeToMySQLDateTime(time());
        unset($input['parameters']);

        $id = $this->_measureRepository->create($input);
        $this->_measureRepository->setParameters($id, $params);

        $this->setData($id);
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
        $id             = (int)$params['id'];
        $data           = $this->getRequest()->getInput();
        $params         = $data['parameters'];
        $data['edited'] = Performance_Main_Database::convertTimeToMySQLDateTime(time());
        unset($data['parameters']);

        $this->_measureRepository->update($id, $data);
        $this->_measureRepository->deleteParameters($id);
        $this->_measureRepository->setParameters($id, $params);
    }

    /**
     * Find all attempts for measure by measure id.
     *
     * @link /measure/{id}/attempts
     *
     * @method GET
     *
     * @return void
     */
    public function actionAttempts($params) {
        $data = $this->_attemptRepository->getAttempts($params['id']);

        $this->setData(array_values($data));
    }

    /**
     * Get attempt by attempt id.
     *
     * @link /measure/attempt/{id}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetAttempt($params) {
        $data = $this->_attemptRepository->getAttempt($params['id']);

        $this->setData($data);
    }

    /**
     * Method for delete attempt by id.
     *
     * @link /measure/attempt/{id}
     *
     * @method DELETE
     *
     * @return void
     */
    public function actionDeleteAttempt($params) {
        $this->_attemptRepository->delete($params['id']);
        $this->setData(true);
    }

    /**
     * Start measure for attempt by attempt id.
     *
     * @link /measure/attempt/{id}/start
     *
     * @method POST
     *
     * @return void
     */
    public function actionStartMeasure($params) {
        $this->getProvider()
            ->get('Performance_Profiler_Gearman_Client')
            ->setData(array('id' => $params['id']))
            ->doAsynchronize();

        $this->setData(true);
    }

    /**
     * Gets statistics for attempt by attempt id.
     *
     * @link /measure/attempt/{id}/statistic
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetAttemptStatistic($params) {
        $data = $this->_statisticRepository->getAttemptStatistic($params['id']);

        $this->setData($data);
    }

    /**
     * Gets function statistics for attempt by attempt id.
     *
     * @link /measure/attempt/{id}/statistic/function
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
     * Gets calls stack for attempt by attempt id and parent call id (zero is all root calls)
     *
     * @link /measure/attempt/{id}/callStack/parent/{parentId}
     *
     * @method GET
     *
     * @return void
     */
    public function actionGetAttemptCallStack($params) {
        $data = $this->_statisticDataRepository->getAttemptCallStack($params['id'], $params['parentId']);

        $this->setData($data);
    }
}
