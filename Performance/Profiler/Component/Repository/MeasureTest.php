<?php

namespace PF\Profiler\Component\Repository;

use PF\Main\Abstracts\Repository;
use PF\Profiler\Enum\AttemptState;

/**
 * This script defines repository for measures tests.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class MeasureTest extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('measure_test');
    }

    /**
     * Find all tests for measure by given measure id.
     *
     * @param int $measureId ID of measure
     *
     * @return array
     */
    public function getTests($measureId = null) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName());

        if ($measureId) {
            $select->where('measureId IN (:ids)', array(':ids' => $measureId));
        }

        return $select->fetchAll();
    }

    /**
     * Gets test by given test id.
     *
     * @param int $id ID of test
     *
     * @return array
     */
    public function getTest($id) {
        $select = $this->getDatabase()
            ->select()
            ->from($this->getTableName())
            ->where('id = ?', $id);

        return $select->fetchAll();
    }

    /**
     * Find basic statistics data for attempt by given attempt id.
     *
     * @param int $id ID of attempt
     *
     * @return array
     */
    public function getAttemptStatistic($id) {
       $select = $this->getDatabase()
                ->select()
                ->from(array('ta' => 'test_attempt'))
                ->joinInner(array('asd' => 'attempt_statistic_data'), 'ta.id = asd.attemptId', array())
                ->joinInner(array('ad' => 'attempt_data'), 'ta.id = ad.attemptId', array())
                ->columns(
                    array(
                        'maxImmersion' => 'MAX(ad.immersion)',
                        'started'      => 'UNIX_TIMESTAMP(ta.started)*1000'
                    )
                )
                ->where('ta.id = :id', array('id' => $id));

        return $select->fetchOne();
    }

    /**
     * Create new test for measure by given test data.
     *
     * @param array $data Data for test
     *
     * @return int
     */
    public function create($data) {
        $data['state']   = AttemptState::STATE_IDLE;
        $data['started'] = $this->getUtils()->convertTimeToMySQLDateTime(time());
        return parent::create($data);
    }

    /**
     * Update test by given data and test id.
     *
     * @param int   $id   ID of test
     * @param array $data Data to update
     *
     * @return int
     */
    public function update($id, $data) {
        if (isset($data['started'])) {
            unset($data['started']);
        }

        return parent::update($id, $data);
    }

    /**
     * Deletes test by given test id.
     *
     * @param int $id ID of test
     *
     * @return int
     */
    public function delete($id) {
        return parent::delete($id);
    }
}
