<?php

/**
 * This script defines repository for measure statistic.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_MeasureStatistic extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('profiler_measure_statistic');
    }

    /**
     * Gets statistics data by given attempt id.
     *
     * @param int $id ID of attempt
     *
     * @return array Array with statistics
     */
    public function getAttemptStatistic($id) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('pms' => 'profiler_measure_statistic'))
                ->joinInner(array('pmd' => 'profiler_measure_data'), 'pms.profiler_measure_attempt_id = pmd.profiler_measure_attempt_id', array())
                ->joinInner(array('pma' => 'profiler_measure_attempt'), 'pms.profiler_measure_attempt_id = pma.id', array())
                ->columns(
                    array(
                        'maxImmersion' => 'MAX(pmd.immersion)',
                        'started' => 'UNIX_TIMESTAMP(pma.started)*1000'))
                ->where('pms.profiler_measure_attempt_id = ?', $id);

        return $select->fetchOne();
    }

    /**
     * Create new statistic for attempt.
     *
     * @param array $data Array with data
     *
     * @return array Inserted ID
     */
    public function create($data) {
        return parent::create($data);
    }

    /**
     * Update statistic for attempt by given statistic id and data for update.
     *
     * @param int   $id   ID of statistic
     * @param array $data Data for update
     *
     * @return int Count of affected rows
     */
    public function update($id, $data) {
        return parent::update($id, $data);
    }

    /**
     * Delete statistic by given statistic id.
     *
     * @param int $id ID of statistic
     *
     * @return int Count of affectted rows
     */
    public function delete($id) {
        return parent::delete($id);
    }
}
