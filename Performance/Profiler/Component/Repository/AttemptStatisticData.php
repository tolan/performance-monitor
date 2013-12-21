<?php

/**
 * This script defines repository for measures statistic data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_AttemptStatisticData extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('attempt_statistic_data');
    }

    /**
     * Return calls by given attempt id and parent id.
     *
     * @param int $attemptId ID of attempt
     * @param int $parentId  ID of parent (default is 0)
     *
     * @return array Array with calls
     */
    public function getAttemptCallStack($attemptId, $parentId = 0) {
        $select = $this->getDatabase()
            ->select()
            ->from(array('asd' => 'attempt_statistic_data'))
            ->where('asd.attemptId = ?', $attemptId)
            ->where('asd.parentId = ?', $parentId);

        return $select->fetchAll();
    }

    /**
     * Returns statistics for all function calls by given attempt id.
     *
     * @param int $attemptId ID of attempt
     *
     * @return array Array with statistics of all calls (functions)
     */
    public function getAttemptFunctionStatistic($attemptId) {
        $select = $this
            ->getDatabase()
            ->select()
            ->columns(
                array(
                    'id'       => 'asd.id',
                    'file'     => 'asd.file',
                    'line'     => 'asd.line',
                    'content'  => 'asd.content',
                    'time'     => 'SUM(asd.time)',
                    'avgTime'  => 'AVG(asd.time)',
                    'count'    => 'COUNT(CONCAT(asd.file, asd.line))',
                    'min'      => 'MIN(asd.time)',
                    'max'      => 'MAX(asd.time)'
                )
            )
            ->from(array('asd' => 'attempt_statistic_data'), array())
            ->where('asd.attemptId = ?', $attemptId)
            ->group(array('asd.file', 'asd.line'));

        $data = $select->fetchAll();

        foreach ($data as &$call) {
            $call['id']      = (int)$call['id'];
            $call['line']    = (int)$call['line'];
            $call['time']    = (float)$call['time'];
            $call['avgTime'] = (float)$call['avgTime'];
            $call['count']   = (int)$call['count'];
            $call['min']     = (float)$call['min'];
            $call['max']     = (float)$call['max'];
        }

        return $data;
    }

    /**
     * Create new statistics data.
     *
     * @param array $data Array with statistic data
     *
     * @return int Inserted ID
     */
    public function create($data) {
        return parent::create($data);
    }

    /**
     * Update statistic data by given id and data for update.
     *
     * @param int   $id   ID of statistic data
     * @param array $data Array with data for update
     *
     * @return int Count of affected rows
     */
    public function update($id, $data) {
        return parent::update($id, $data);
    }

    /**
     * Delete statistic data by given id.
     *
     * @param int $id ID of statistic data
     *
     * @return int Count of affected rows
     */
    public function delete($id) {
        return parent::delete($id);
    }
}
