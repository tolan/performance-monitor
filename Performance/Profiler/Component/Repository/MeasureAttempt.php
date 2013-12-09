<?php

/**
 * This script defines repository for measures attempts.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_MeasureAttempt extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('profiler_measure_attempt');
    }

    /**
     * Returns attempts for meassure by given measure id (optional you can select attempt ids).
     *
     * @param int|array $measureId ID/s of measure
     * @param array     $ids       ID/s of attempts (optional)
     *
     * @return array Array with attempts
     */
    public function getAttempts($measureId, $ids = null) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('pma' => 'profiler_measure_attempt'))
                ->where('profiler_measure_id = ?', $measureId);

        if ($ids) {
            $select->where('pma.id IN (?)', $ids);
        }

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $key => $item) {
            $item['started'] = strtotime($item['started'])*1000;
            $result[$item['id']] = $item;
        }

        return $result;
    }

    /**
     * Gets attempt by given id.
     *
     * @param int $id ID of attempt
     *
     * @return array Array with attempt data
     */
    public function getAttempt($id) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('pma' => 'profiler_measure_attempt'))
                ->where('id = ?', $id);

        $data = $select->fetchOne();
        $data['started'] = strtotime($data['started'])*1000;

        return $data;
    }

    /**
     * Create new attempt with given data.
     *
     * @param array $data Array with data
     *
     * @return int Inseted id
     */
    public function create($data) {
        $data['started'] = Performance_Main_Database::convertTimeToMySQLDateTime($data['started']);
        return parent::create($data);
    }

    /**
     * Update attempt by given attempt id and data for update.
     *
     * @param int   $id   ID of attempt
     * @param array $data Array with data for update
     *
     * @return int Count of affected rows
     */
    public function update($id, $data) {
        return parent::update($id, $data);
    }

    /**
     * Delete attempt by given id.
     *
     * @param int $id ID of attempt
     *
     * @return int count of affected rows
     */
    public function delete($id) {
        return parent::delete($id);
    }
}
