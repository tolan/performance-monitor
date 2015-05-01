<?php

namespace PM\Cron\Repository;

use PM\Main\Abstracts\Repository;
use PM\Cron;

/**
 * This script defines class for manage cron trigger logs.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class TriggerLog extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('cron_trigger_log');
    }

    /**
     * It returns logs for trigger.
     *
     * @param int $triggerId Id of cron trigger.
     *
     * @return Cron\Entity\TriggerLog[]
     */
    public function findLogsForTrigger($triggerId) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('cronTriggerId = ?', $triggerId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']            = (int)$item['id'];
            $item['cronTriggerId'] = (int)$item['cronTriggerId'];
            $item['started']       = $this->getUtils()->convertTimeFromMySQLDateTime($item['started']);

            $result[] = new Cron\Entity\TriggerLog($item);
        }

        return $result;
    }

    /**
     * It returns last log for trigger.
     *
     * @param int $triggerId Id of cron trigger.
     *
     * @return Cron\Entity\TriggerLog
     */
    public function getLastLogForTrigger($triggerId) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('cronTriggerId = ?', $triggerId)
                ->order('started DESC')
                ->limit(1);

        $data   = $select->fetchOne();
        $result = null;

        if (!empty($data)) {
            $data['id']            = (int)$data['id'];
            $data['cronTriggerId'] = (int)$data['cronTriggerId'];
            $data['started']       = $this->getUtils()->convertTimeFromMySQLDateTime($data['started'], false);

            $result = new Cron\Entity\TriggerLog($data);
        }

        return $result;
    }

    /**
     * It creates new trigger log.
     *
     * @param Cron\Entity\TriggerLog $log Cron trigger log instance
     *
     * @return Cron\Entity\TriggerLog
     */
    public function createLog(Cron\Entity\TriggerLog $log) {
        $data = array(
            'cronTriggerId' => $log->get('cronTriggerId'),
            'started'       => $this->getUtils()->convertTimeToMySQLDateTime($log->get('started', time())),
            'state'         => $log->get('state', Cron\Enum\TriggerState::IDLE),
            'message'       => $log->get('message', '')
        );
        $id = parent::create($data);

        return $log->setId($id);
    }

    /**
     * It updates existed cron trigger log.
     *
     * @param Cron\Entity\TriggerLog $log Cron trigger log instance
     *
     * @return int
     */
    public function updateLog(Cron\Entity\TriggerLog $log) {
        $data = array(
            'cronTriggerId' => $log->get('cronTriggerId'),
            'started'       => $this->getUtils()->convertTimeToMySQLDateTime($log->get('started', time())),
            'state'         => $log->get('state', Cron\Enum\TriggerState::IDLE),
            'message'       => $log->get('message', '')
        );

        return parent::update($log->getId(), $data);
    }

    /**
     * It deletes cron trigger log.
     *
     * @param int $id Id of cron trigger log
     *
     * @return int
     */
    public function deleteLog($id) {
        return parent::delete($id);
    }
}
