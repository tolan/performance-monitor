<?php

namespace PM\Cron\Repository;

use PM\Main\Abstracts\Repository;
use PM\Cron;

/**
 * This script defines class for manage cron trigger.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Trigger extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('cron_trigger');
    }

    /**
     * It returns cron timers for task.
     *
     * @param int $taskId Id of task
     *
     * @return Cron\Entity\Timer
     */
    public function getTimersForTask($taskId) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('cronId = ?', $taskId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']     = (int)$item['id'];
            $item['cronId'] = (int)$item['cronId'];

            $result[] = new Cron\Entity\Timer($item);
        }

        return $result;
    }

    /**
     * It returns cron timer.
     *
     * @param int $id Id of cron timer
     *
     * @return Cron\Entity\Timer
     */
    public function getTimer($id) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('id = ?', $id);

        $data = $select->fetchOne();

        $data['id']     = (int)$data['id'];
        $data['cronId'] = (int)$data['cronId'];

        $result = new Cron\Entity\Timer($data);

        return $result;
    }

    /**
     * It creates new cron timer.
     *
     * @param Cron\Entity\Timer $trigger Cron timer instance
     *
     * @return Cron\Entity\Timer
     */
    public function createTimer(Cron\Entity\Timer $trigger) {
        $data = array(
            'cronId'    => $trigger->get('cronId'),
            'dayOfWeek' => $trigger->get('dayOfWeek', '*'),
            'month'     => $trigger->get('month', '*'),
            'day'       => $trigger->get('day', '*'),
            'hour'      => $trigger->get('hour', '*'),
            'minute'    => $trigger->get('minute', '*')
        );
        $id = parent::create($data);

        return $trigger->setId($id);
    }

    /**
     * It updates existed cron timer.
     *
     * @param Cron\Entity\Timer $trigger Cron timer instance
     *
     * @return int
     */
    public function updateTimer(Cron\Entity\Timer $trigger) {
        $data = array(
            'cronId'    => $trigger->get('cronId'),
            'dayOfWeek' => $trigger->get('dayOfWeek', '*'),
            'month'     => $trigger->get('month', '*'),
            'day'       => $trigger->get('day', '*'),
            'hour'      => $trigger->get('hour', '*'),
            'minute'    => $trigger->get('minute', '*')
        );

        return parent::update($trigger->getId(), $data);
    }

    /**
     * It deletes cron timer.
     *
     * @param int $id Id of cron timer
     *
     * @return int
     */
    public function deleteTimer($id) {
        return parent::delete($id);
    }
}
