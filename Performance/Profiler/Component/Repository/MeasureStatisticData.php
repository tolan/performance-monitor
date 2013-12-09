<?php

/**
 * This script defines repository for measures statistic data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Repository_MeasureStatisticData extends Performance_Main_Abstract_Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('profiler_measure_statistic_data');
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
        return $this
            ->getDatabase()
            ->select()
            ->from(array('pms' => 'profiler_measure_statistic'), array())
            ->joinInner(array('pmsd' => 'profiler_measure_statistic_data'), 'pmsd.profiler_measure_statistic_id = pms.id')
            ->where('pms.profiler_measure_attempt_id = ?', $attemptId)
            ->where('pmsd.parent_id = ?', $parentId)
            ->fetchAll();
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
                    'id'       => 'pmsd.id',
                    'file'     => 'pmsd.file',
                    'line'     => 'pmsd.line',
                    'content'  => 'pmsd.content',
                    'time'     => 'SUM(pmsd.time)',
                    'avgTime'  => 'AVG(pmsd.time)',
                    'count'    => 'COUNT(CONCAT(pmsd.file, pmsd.line))',
                    'min'      => 'MIN(pmsd.time)',
                    'max'      => 'MAX(pmsd.time)',
                    'times'    => 'CONCAT(\'[\', GROUP_CONCAT(pmsd.time), \']\')'
                )
            )
            ->from(array('pms' => 'profiler_measure_statistic'), array())
            ->joinInner(array('pmsd' => 'profiler_measure_statistic_data'), 'pmsd.profiler_measure_statistic_id = pms.id', array())
            ->where('pms.profiler_measure_attempt_id = ?', $attemptId)
            ->group(array('pmsd.file', 'pmsd.line'));

        $data = $select->fetchAll();

        foreach ($data as &$call) {
            $call['id']      = (int)$call['id'];
            $call['line']    = (int)$call['line'];
            $call['time']    = (float)$call['time'];
            $call['avgTime'] = (float)$call['avgTime'];
            $call['count']   = (int)$call['count'];
            $call['min']     = (float)$call['min'];
            $call['max']     = (float)$call['max'];
            $call['times']   = json_decode($call['times']);
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
