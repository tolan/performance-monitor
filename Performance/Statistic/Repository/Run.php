<?php

namespace PM\Statistic\Repository;

use PM\Main\Abstracts\Repository;
use PM\Statistic\Entity;
use PM\Statistic\Enum\Run\State;

/**
 * This script defines class for run repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Run extends Repository {

    const DATA_TABLE = 'statistic_set_template_data';

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('statistic_set_run');
    }

    /**
     * Return all runs for statistic set.
     *
     * @param int $setId Id of statistic set
     *
     * @return Entity\Run[]
     */
    public function getRunsForSet($setId) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('statisticSetId = ?', $setId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $run) {
            $run['id']             = (int)$run['id'];
            $run['statisticSetId'] = (int)$run['statisticSetId'];
            $run['started']        = $this->getUtils()->convertTimeFromMySQLDateTime($run['started']);

            $result[] = new Entity\Run($run);
        }

        return $result;
    }

    /**
     * Returns statistic run entity by given id.
     *
     * @param int $id Id of statistic run
     *
     * @return Entity\Run
     */
    public function getRun($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data = $select->fetchOne();

        $data['id']             = (int)$data['id'];
        $data['statisticSetId'] = (int)$data['statisticSetId'];
        $data['started']        = $this->getUtils()->convertTimeFromMySQLDateTime($data['started']);

        $run = new Entity\Run($data);

        return $run;
    }

    /**
     * Creates statistic run entity in database.
     *
     * @param Entity\Run $run Statistic run entity instance
     *
     * @return Entity\Run
     */
    public function createRun(Entity\Run $run) {
        $data = array(
            'statisticSetId' => $run->get('statisticSetId'),
            'state'          => State::IDLE,
            'started'        => null
        );

        $id = parent::create($data);

        return $run->setId($id);
    }

    /**
     * Updates statistic run entity by given instance.
     *
     * @param Entity\Run $run Statistic run entity instance
     *
     * @return int
     */
    public function updateRun(Entity\Run $run) {
        $started = $run->get('started') === null ? $run->get('started') : $this->getUtils()->convertTimeToMySQLDateTime($run->get('started'));
        $data    = array(
            'statisticSetId' => $run->get('statisticSetId'),
            'state'          => $run->get('state'),
            'started'        => $started
        );

        return parent::update($run->getId(), $data);
    }

    /**
     * Deletes statistic run entity from database by given id.
     *
     * @param int $id Id of statistic run
     *
     * @return int
     */
    public function deleteRun($id) {
        return parent::delete($id);
    }

    /**
     * Returns statistic data entities for statistic run.
     *
     * @param int $id Id of statistic run
     *
     * @return Entity\Statistic[]
     */
    public function getDataForRun($id) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('data' => self::DATA_TABLE))
                ->where('statisticSetRunId = ?', $id);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $statistic) {
            $statistic['statisticSetRunId']   = (int)$statistic['statisticSetRunId'];
            $statistic['statisticViewLineId'] = (int)$statistic['statisticViewLineId'];
            $statistic['time']                = empty($statistic['time']) ? null : $this->getUtils()->convertTimeFromMySQLDateTime($statistic['time']);
            $statistic['value']               = (float)$statistic['value'];

            $result[] = new Entity\Statistic($statistic);
        }

        return $result;
    }

    /**
     * Returns list of all attached statistic template ids for statistic run.
     *
     * @param int $id Id of statistic run
     *
     * @return array
     */
    public function getTemplateIdsForRun($id) {
        $select = $this->getDatabase()
                ->select()
                ->from(array('sstd' => $this->getTableName(), array()))
                ->joinInner(array('sst' => 'statistic_set_template'), 'sst.statisticSetId = sstd.statisticSetId', array())
                ->joinInner(array('st' => 'statistic_template'), 'st.id = sst.statisticTemplateId', array('id'))
                ->where('sstd.id = ?', $id);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $template) {
            $result[] = (int)$template['id'];
        }

        return $result;
    }

    /**
     * Creates new statistic data entity into database.
     *
     * @param Entity\Statistic $statistic Statistic data entity instance
     *
     * @return Entity\Statistic
     */
    public function createStatistic(Entity\Statistic $statistic) {
        $data = array(
            'statisticSetRunId'   => $statistic->getStatisticSetRunId(),
            'statisticViewLineId' => $statistic->getStatisticViewLineId(),
            'time'                => $statistic->getTime() === null ? null : $this->getUtils()->convertTimeToMySQLDateTime($statistic->getTime()),
            'value'               => $statistic->getValue()
        );

        $id = parent::create($data, self::DATA_TABLE);

        return $statistic->setId($id);
    }
}
