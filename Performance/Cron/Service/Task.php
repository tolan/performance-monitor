<?php

namespace PM\Cron\Service;

use PM\Main\Abstracts;
use PM\Cron;

/**
 * This script defines class for task service of cron.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Task extends Abstracts\Service {

    /**
     * Find all cron tasks.
     *
     * @param Cron\Repository\Task $repository Cron task repository instance
     *
     * @return array
     */
    public function findTasks(Cron\Repository\Task $repository) {
        return $repository->findTasks();
    }

    /**
     * Return cron task by given id.
     *
     * @param int                  $id             Id of cron task
     * @param Cron\Repository\Task $repository     Cron task repository instance
     * @param Cron\Service\Trigger $triggerService Cron trigger service instance
     *
     * @return Cron\Entity\Task
     */
    public function getTask($id, Cron\Repository\Task $repository, Cron\Service\Trigger $triggerService) {
        $task = $repository->getTask($id);

        $triggers = $this->getExecutor()
            ->add('getTriggersForTask', $triggerService, array('taskId' => $id))
            ->execute()
            ->getData();

        $task->set('triggers', $triggers);

        return $task;
    }

    /**
     * Creates new cron task by given data.
     *
     * @param array                $data           Data of new task
     * @param Cron\Repository\Task $repository     Task repository instance
     * @param Cron\Service\Trigger $triggerService Trigger service instance
     *
     * @return Cron\Entity\Task
     */
    public function createTask($data, Cron\Repository\Task $repository, Cron\Service\Trigger $triggerService) {
        $task = new Cron\Entity\Task($data);

        $repository->createTask($task);
        $executor = $this->getExecutor()->add('createTrigger', $triggerService);

        $triggers = array();
        foreach ($task->getTriggers() as $trigger) {
            $trigger['cronId'] = $task->getId();
            $executor->getResult()->set('data', $trigger);
            $triggers[] = $executor->execute()->getData();
        }

        $task->setTriggers($triggers);

        return $task;
    }

    /**
     * Updates existed cron task by given data.
     *
     * @param array                $data           Data of cron task with new data
     * @param Cron\Repository\Task $repository     Cron task repository instance
     * @param Cron\Service\Trigger $triggerService Cron trigger service instance
     *
     * @return Cron\Entity\Task
     */
    public function updateTask($data, Cron\Repository\Task $repository, Cron\Service\Trigger $triggerService) {
        $taskUpdate = new Cron\Entity\Task($data);
        $task       = $this->getExecutor()
            ->clean()
            ->add('getTask', $this, array('id' => $taskUpdate->getId()))
            ->execute()
            ->getData();

        $repository->updateTask($taskUpdate);

        $this->_updateTriggers($taskUpdate, $task, $triggerService);

        return $taskUpdate;
    }

    /**
     * Deletes cron task by given id.
     *
     * @param int                  $id         Id of cron task
     * @param Cron\Repository\Task $repository Cron task repository instance
     *
     * @return int
     */
    public function deleteTask($id, Cron\Repository\Task $repository) {
        return $repository->deleteTask($id);
    }

    /**
     * Updates triggers of task instance.
     *
     * @param Cron\Entity\Task     $taskUpdate     Existed cron task instance
     * @param Cron\Entity\Task     $task           Cron task instance with new triggers
     * @param Cron\Service\Trigger $triggerService Cron trigger service instance
     *
     * @return Task
     */
    private function _updateTriggers(Cron\Entity\Task $taskUpdate, Cron\Entity\Task $task, Cron\Service\Trigger $triggerService) {
        $existed = array();

        foreach ($task->getTriggers() as $trigger) {
            $timer                    = $trigger->getTimer();
            $existed[$timer->getId()] = $trigger;
        }

        $toCreate = array();
        $toUpdate = array();

        foreach ($taskUpdate->getTriggers() as $trigger) {
            $timer = $trigger['timer'];
            if (array_key_exists('id', $timer) === false || array_key_exists($timer['id'], $existed) === false) {
                $toCreate[] = $trigger;
            } elseif(array_key_exists($timer['id'], $existed) === true) {
                $toUpdate[$timer['id']] = $trigger;
            }
        }

        $toDelete = array_diff_key($existed, $toUpdate);

        $executor = $this->getExecutor()->clean()->add('createTrigger', $triggerService);
        foreach ($toCreate as $trigger) {
            $trigger['cronId'] = $taskUpdate->getId();
            $executor->getResult()->set('data', $trigger);
            $executor->execute();
        }

        $executor->clean()->add('updateTrigger', $triggerService);
        foreach ($toUpdate as $trigger) {
            $trigger['cronId'] = $taskUpdate->getId();
            $timer             = $trigger['timer'];
            $executor->getResult()
                ->set('data', $trigger)
                ->set('oldTrigger', $existed[$timer['id']]);
            $executor->execute();
        }

        $executor->clean()->add('deleteTrigger', $triggerService);
        foreach (array_keys($toDelete) as $triggerId) {
            $executor->getResult()->set('id', $triggerId);
            $executor->execute();
        }

        return $this;
    }
}
