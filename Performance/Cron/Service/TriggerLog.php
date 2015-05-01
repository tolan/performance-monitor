<?php

namespace PM\Cron\Service;

use PM\Main\Abstracts;
use PM\Cron;

/**
 * This script defines class for trigger log service of cron.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class TriggerLog extends Abstracts\Service {

    /**
     * Returns logs for trigger.
     *
     * @param int                        $triggerId  ID of cron trigger
     * @param Cron\Repository\TriggerLog $repository Cron trigger log repository instance
     *
     * @return Cron\Entity\TriggerLog[]
     */
    public function findLogsForTrigger($triggerId, Cron\Repository\TriggerLog $repository) {
        return $repository->findLogsForTrigger($triggerId);
    }

    /**
     * Returns last logs for trigger.
     *
     * @param int                        $triggerId  ID of cron trigger
     * @param Cron\Repository\TriggerLog $repository Cron trigger log repository instance
     *
     * @return Cron\Entity\TriggerLog
     */
    public function getLastLogForTrigger($triggerId, Cron\Repository\TriggerLog $repository) {
        return $repository->getLastLogForTrigger($triggerId);
    }

    /**
     * Creates new trigger log by given data.
     *
     * @param array                      $data       Data of new trigger log
     * @param Cron\Repository\TriggerLog $repository Cron trigger log repository instance
     *
     * @return Cron\Entity\TriggerLog
     */
    public function createLog($data, Cron\Repository\TriggerLog $repository) {
        $log = new Cron\Entity\TriggerLog($data);

        return $repository->createLog($log);
    }

    /**
     * Updates existed cron trigger log by given data.
     *
     * @param array                      $data       Data of cron trigger log with new data
     * @param Cron\Repository\TriggerLog $repository Cron trigger log repository instance
     *
     * @return Cron\Entity\TriggerLog
     */
    public function updateLog($data, Cron\Repository\TriggerLog $repository) {
        $log = new Cron\Entity\TriggerLog($data);

        $repository->updateLog($log);

        return $log;
    }
}
