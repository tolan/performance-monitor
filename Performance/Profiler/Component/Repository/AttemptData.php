<?php

/**
 * This script defines repository for measures attempt data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_AttemptData extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('attempt_data');
    }

    /**
     * Gets data by given attempt id. It returns calls for attempt.
     *
     * @param int $id ID of attempt
     *
     * @return array
     */
    public function getDataByAttemptId($id) {
        return $this->getDatabase()
            ->select()
            ->from($this->getTableName(), array('id', 'file', 'line', 'immersion', 'start', 'end'))
            ->where('attemptId = ?', $id)
            ->fetchAll();
    }

    /**
     * Create new data (call) by given data.
     *
     * @param array $data Array with data
     *
     * @return int Inserted ID
     */
    public function create($data) {
        return parent::create($data);
    }

    /**
     * Update data (call) by given attempt data id and data to update.
     *
     * @param int   $id   ID of attempt data
     * @param array $data Array with data for update
     *
     * @return int Count of affected rows
     */
    public function update($id, $data) {
        return parent::update($id, $data);
    }

    /**
     * Delete data (call) by given attempt data id.
     *
     * @param int $id ID of attempt data
     *
     * @return int Count of affected rows
     */
    public function delete($id) {
        return parent::delete($id);
    }
}
