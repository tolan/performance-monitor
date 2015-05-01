<?php

namespace PM\Cron\Repository;

use PM\Main\Abstracts\Repository;
use PM\Cron;

/**
 * This script defines class for manage cron action tasks.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class ActionTask extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('cron_trigger_action');
    }

    /**
     * It returns tasks for cron action.
     *
     * @param int $actionId Id of cron action
     *
     * @return Cron\Entity\ActionTask[]
     */
    public function getTasksForAction($actionId) {
        $select = $this->getDatabase()
                ->select()
                ->from($this->getTableName())
                ->where('cronTriggerSourceId = ?', $actionId);

        $data = $select->fetchAll();

        $result = array();
        foreach ($data as $item) {
            $item['id']                   = (int)$item['id'];
            $item['cronTriggerSourceId']  = (int)$item['cronTriggerSourceId'];
            $item                        += json_decode($item['data'], true);
            unset($item['data']);

            $result[] = new Cron\Entity\ActionTask($item);
        }

        return $result;
    }

    /**
     * It creates new cron action task.
     *
     * @param Cron\Entity\ActionTask $actionTask Cron action task instance
     *
     * @return Cron\Entity\ActionTask
     */
    public function createActionTask(Cron\Entity\ActionTask $actionTask) {
        $sourceId = $actionTask->get('cronTriggerSourceId');
        $actionTask->reset('cronTriggerSourceId');
        $data = array(
            'cronTriggerSourceId' => $sourceId,
            'data'                => json_encode($actionTask->toArray()),
        );
        $id = parent::create($data);

        return $actionTask->setId($id);
    }

    /**
     * It updates existed cron action task.
     *
     * @param Cron\Entity\ActionTask $actionTask Cron action task instance
     *
     * @return int
     */
    public function updateActionTask(Cron\Entity\ActionTask $actionTask) {
        $id       = $actionTask->getId();
        $sourceId = $actionTask->get('cronTriggerSourceId');
        $actionTask->reset('cronTriggerSourceId');
        $actionTask->reset('id');
        $data = array(
            'cronTriggerSourceId' => $sourceId,
            'data'                => json_encode($actionTask->toArray()),
        );

        return parent::update($id, $data);
    }

    /**
     * It deletes cron action task.
     *
     * @param int $id Id of cron action task
     *
     * @return int
     */
    public function deleteActionTask($id) {
        return parent::delete($id);
    }
}
