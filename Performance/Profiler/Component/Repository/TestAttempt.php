<?php

class Performance_Profiler_Component_Repository_TestAttempt extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('test_attempt');
    }

    /**
     * Find all attempt for test by given test id.
     *
     * @param int $testId ID of test
     *
     * @return array
     */
    public function getAttempts($testId = null) {
        $select = $this->getDatabase()
            ->select()
            ->from($this->getTableName());

        if ($testId) {
            $select->where('testId IN (:id)', array(':id' => $testId));
        }

        return $select->fetchAll();
    }

    /**
     * Returns attempt by given attempt id.
     *
     * @param int $id ID of attempt
     *
     * @return array
     */
    public function getAttempt($id) {
        $select = $this->getDatabase()
            ->select()
            ->from($this->getTableName())
            ->where('id = :id', array(':id' => $id));

        return $select->fetchOne();
    }

    /**
     * Create new attempt for test by given data.
     *
     * @param array $data Data of attempt
     *
     * @return int
     */
    public function create($data) {
        return parent::create($data);
    }

    /**
     * Updates attempt for test with given data.
     *
     * @param int   $id   ID of attempt
     * @param array $data Data for update
     *
     * @return int
     */
    public function update($id, $data) {
        if (isset($data['started'])) {
            $data['started'] = Performance_Main_Database::convertTimeToMySQLDateTime($data['started']);
        }

        return parent::update($id, $data);
    }
}
