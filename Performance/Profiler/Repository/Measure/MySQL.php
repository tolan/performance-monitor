<?php

namespace PF\Profiler\Repository\Measure;

use PF\Main\Abstracts\Repository;
use PF\Profiler\Repository\Interfaces;
use PF\Profiler\Monitor\Storage\State;
use PF\Profiler\Entity;

/**
 * This script defines class for measure repository working with MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class MySQL extends Repository implements Interfaces\Measure {

    const STATS_TABLE   = 'measure_statistic_data';

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('test_measure');
    }

    /**
     * Creates new measure in repository with basic data.
     *
     * @param \PF\Profiler\Entity\Measure $measure Measure entity instance
     *
     * @return type
     */
    public function createMeasure(Entity\Measure $measure) {
        $data = array(
            'testId'     => $measure->get('testId'),
            'url'        => $measure->get('url'),
            'method'     => $measure->get('method'),
            'parameters' => $measure->get('parameters', ''),
            'body'       => $measure->get('body', ''),
            'state'      => $measure->get('state', State::STATE_EMPTY)
        );
        $id = parent::create($data);

        return $measure->setId($id);
    }

    /**
     * Find measures for test.
     *
     * @param int $testId ID of test
     *
     * @return \PF\Profiler\Entity\Measure[]
     */
    public function findMeasuresForTest($testId) {
        $select = $this->getDatabase()
            ->select()
            ->from($this->getTableName())
            ->where('testId = ?', $testId);

        $data   = $select->fetchAll();
        $result = array();

        foreach ($data as $item) {
            $item['id']      = (int)$item['id'];
            $item['testId']  = (int)$item['testId'];
            $item['started'] = $this->getUtils()->convertTimeFromMySQLDateTime($item['started']);
            $item['time']    = (float)$item['time'];
            $item['calls']   = (int)$item['calls'];

            $result[] = new Entity\Measure($item);
        }

        return $result;
    }

    /**
     * Get measure statistics like as count of calls, consumed time, date of start, etc.
     *
     * @param int $measureId ID of measure
     *
     * @return array
     */
    public function getMeasureStatistics($measureId) {
        $select = $this->getDatabase()
            ->select()
            ->from(array('mes' => $this->getTableName()))
            ->joinInner(array('stat' => self::STATS_TABLE), 'mes.id = stat.measureId', array())
            ->columns(
                array(
                    'maxImmersion' => 'MAX(stat.immersion)'
                )
            )
            ->where('mes.id = :id', array(':id' => $measureId));

        $data = $select->fetchOne();

        $data['id']           = (int)$data['id'];
        $data['testId']       = (int)$data['testId'];
        $data['started']      = $this->getUtils()->convertTimeFromMySQLDateTime($data['started']);
        $data['time']         = (float)$data['time'];
        $data['calls']        = (int)$data['calls'];
        $data['maxImmersion'] = (int)$data['maxImmersion'];

        $callSelect = $this->getDatabase()
            ->select()
            ->from(self::STATS_TABLE)
            ->columns('time + timeSubStack as timeSubStack')
            ->where('measureId = ?', $measureId)
            ->order('time DESC')
            ->limit(1);

        $slowestCall = $callSelect->fetchOne();

        $slowestCall['id']           = (int)$slowestCall['id'];
        $slowestCall['measureId']    = (int)$slowestCall['measureId'];
        $slowestCall['parentId']     = (int)$slowestCall['parentId'];
        $slowestCall['line']         = (int)$slowestCall['line'];
        $slowestCall['lines']        = (int)$slowestCall['lines'];
        $slowestCall['time']         = (float)$slowestCall['time'];
        $slowestCall['timeSubStack'] = (float)$slowestCall['timeSubStack'];
        $slowestCall['immersion']    = (int)$slowestCall['immersion'];

        $data['slowestCall'] = $slowestCall;

        return $data;
    }

    /**
     * Get list of calls by given measure ID and ID of parent call.
     *
     * @param int $measureId ID of measure
     * @param int $parentId  ID of parent call (default 0 it means get root calls)
     *
     * @return array
     */
    public function getMeasureCallStack($measureId, $parentId = 0) {
        $select = $this->getDatabase()
            ->select()
            ->from(self::STATS_TABLE)
            ->where('measureId = :measureId', array(':measureId' => $measureId))
            ->where('parentId = :parentId', array(':parentId' => $parentId));

        $data = $select->fetchAll();

        foreach ($data as &$item) {
            $item['id']           = (int)$item['id'];
            $item['measureId']    = (int)$item['measureId'];
            $item['parentId']     = (int)$item['parentId'];
            $item['line']         = (int)$item['line'];
            $item['lines']        = (int)$item['lines'];
            $item['time']         = (float)$item['time'];
            $item['timeSubStack'] = (float)$item['timeSubStack'] + (float)$item['time'];
            $item['immersion']    = (int)$item['immersion'];
        }

        return $data;
    }

    /**
     * Get list statistics information about calls grouped by file and line.
     *
     * @param int $measureId ID of measure
     *
     * @return array
     */
    public function getMeasureCallsStatistic($measureId) {
        $select = $this
            ->getDatabase()
            ->select()
            ->columns(
                array(
                    'id'               => 'id',
                    'file'             => 'file',
                    'line'             => 'line',
                    'content'          => 'content',
                    'time'             => 'SUM(time)',
                    'avgTime'          => 'AVG(time)',
                    'minTime'          => 'MIN(time)',
                    'maxTime'          => 'MAX(time)',
                    'timeSubStack'     => 'SUM(timeSubStack) + SUM(time)',
                    'avgTimeSubStack'  => 'AVG(timeSubStack) + AVG(time)',
                    'minTimeSubStack'  => 'MIN(timeSubStack + time)',
                    'maxTimeSubStack'  => 'MAX(timeSubStack + time)',
                    'count'            => 'COUNT(CONCAT(file, line))'
                )
            )
            ->from(self::STATS_TABLE, array())
            ->where('measureId = :id', array(':id' => $measureId))
            ->group(array('file', 'line'));

        $data = $select->fetchAll();

        foreach ($data as &$call) {
            $call['id']              = (int)$call['id'];
            $call['line']            = (int)$call['line'];
            $call['time']            = (float)$call['time'];
            $call['avgTime']         = (float)$call['avgTime'];
            $call['minTime']         = (float)$call['minTime'];
            $call['maxTime']         = (float)$call['maxTime'];
            $call['timeSubStack']    = (float)$call['timeSubStack'];
            $call['avgTimeSubStack'] = (float)$call['avgTimeSubStack'];
            $call['minTimeSubStack'] = (float)$call['minTimeSubStack'];
            $call['maxTimeSubStack'] = (float)$call['maxTimeSubStack'];
            $call['count']           = (int)$call['count'];
        }

        return $data;
    }
}
