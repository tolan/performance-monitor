<?php

namespace PM\Cron\Service;

use PM\Main\Abstracts;
use PM\Cron;

/**
 * This script defines class for action task service of cron trigger.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class ActionTask extends Abstracts\Service {

    /**
     * Returns cron action tasks for cron action.
     *
     * @param int                        $actionId   Id of cron action
     * @param Cron\Repository\ActionTask $repository Action task repository instance
     *
     * @return Cron\Entity\ActionTask[]
     */
    public function getTasksForAction($actionId, Cron\Repository\ActionTask $repository) {
        $tasks = $repository->getTasksForAction($actionId);

        return $tasks;
    }

    /**
     * Creates new action task for cron trigger by given data.
     *
     * @param array                      $data       Data of new action task
     * @param Cron\Repository\ActionTask $repository Action Task repository instance
     *
     * @return Cron\Entity\ActionTask
     */
    public function createActionTask($data, Cron\Repository\ActionTask $repository) {
        $actionTask = new Cron\Entity\ActionTask($data);

        $repository->createActionTask($actionTask);

        return $actionTask;
    }

    /**
     * Updates existed action task for cron trigger by given data.
     *
     * @param array                      $data       Data for update action task
     * @param Cron\Repository\ActionTask $repository Action task repository instance
     *
     * @return Cron\Entity\ActionTask
     */
    public function updateActionTask($data, Cron\Repository\ActionTask $repository) {
        $actionTask = new Cron\Entity\ActionTask($data);

        $repository->updateActionTask($actionTask);

        return $actionTask;
    }

    /**
     * Deletes action task entity.
     *
     * @param innt                       $id         Id of cron action task entity
     * @param Cron\Repository\ActionTask $repository Action task repository instance
     *
     * @return int
     */
    public function deleteActionTask($id, Cron\Repository\ActionTask $repository) {
        return $repository->deleteActionTask($id);
    }
}
