<?php

namespace PM\Cron\Service;

use PM\Main\Abstracts;
use PM\Cron;
use PM\Main\CommonEntity;

/**
 * This script defines class for trigger service of cron.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Trigger extends Abstracts\Service {

    /**
     * Returns cron triggers for cron task. Each trigger contains timer and actions.
     *
     * @param int                     $taskId        ID of cron task
     * @param Cron\Repository\Trigger $repository    Cron trigger repository instance
     * @param Cron\Service\Action     $actionService Cron action service instance
     *
     * @return Cron\Entity\Trigger[]
     */
    public function getTriggersForTask($taskId, Cron\Repository\Trigger $repository, Cron\Service\Action $actionService) {
        $triggers = array();
        $timers   = $repository->getTimersForTask($taskId);
        foreach ($timers as $timer) { /* @var $timer Cron\Entity\Timer */
            $trigger = new Cron\Entity\Trigger();
            $trigger->set('timer', $timer);
            $trigger->setId($timer->getId());

            $triggers[$trigger->getId()] = $trigger;
        }

        $allActions = $this->getExecutor()
            ->add('getActionsForTriggers', $actionService, array('triggersIds' => array_keys($triggers)))
            ->execute()
            ->getData();

        foreach ($allActions as $action) { /* @var $action Cron\Entity\Action */
            $trigger   = $triggers[$action->getCronTriggerId()];
            $actions   = $trigger->get('actions', array());
            $actions[] = $action;
            $trigger->set('actions', $actions);
        }

        return array_values($triggers);
    }

    /**
     * Return cron trigger by given id.
     *
     * @param int                     $id            ID of cron trigger
     * @param Cron\Repository\Trigger $repository    Cron trigger repository instance
     * @param Cron\Service\Action     $actionService Cron action service instance
     *
     * @return Cron\Entity\Trigger
     */
    public function getTrigger($id, Cron\Repository\Trigger $repository, Cron\Service\Action $actionService) {
        $trigger = new Cron\Entity\Trigger();
        $timer   = $repository->getTimer($id);
        $actions = $this->getExecutor()
            ->add('getActionsForTriggers', $actionService, array('triggersIds' => array($id)))
            ->execute()
            ->getData();

        $trigger->setTimer($timer);
        $trigger->setActions($actions);

        return $trigger;
    }

    /**
     * Creates new cron task trigger by given data.
     *
     * @param array                   $data          Data of new trigger
     * @param Cron\Repository\Trigger $repository    Trigger repository instance
     * @param Cron\Service\Action     $actionService Action service instance
     *
     * @return Cron\Entity\Trigger
     */
    public function createTrigger($data, Cron\Repository\Trigger $repository, Cron\Service\Action $actionService) {
        $trigger = new Cron\Entity\Trigger($data);
        $timer   = new Cron\Entity\Timer($trigger->getTimer());
        $timer->setCronId($trigger->getCronId());

        $repository->createTimer($timer);
        $trigger->setId($timer->getId());

        $executor = $this->getExecutor()->add('createAction', $actionService);

        $actions = array();
        foreach ($trigger->getActions() as $action) {
            $action['cronTriggerId'] = $trigger->getId();
            $executor->getResult()->set('data', $action);
            $actions[] = $executor->execute()->getData();
        }

        $trigger->setActions($actions);

        return $trigger;
    }

    /**
     * Updates existed cron trigger by given data.
     *
     * @param array                   $data          Data of cron trigger with new data
     * @param Cron\Repository\Trigger $repository    Cron trigger repository instance
     * @param Cron\Service\Action     $actionService Cron action service instance
     * @param Cron\Entity\Trigger     $oldTrigger    Cron entity trigger with old data
     *
     * @return Cron\Entity\Trigger
     */
    public function updateTrigger($data, Cron\Repository\Trigger $repository, Cron\Service\Action $actionService, Cron\Entity\Trigger $oldTrigger = null) {
        $triggerUpdate = new Cron\Entity\Trigger($data);
        $timer         = new Cron\Entity\Timer($triggerUpdate->getTimer());
        $triggerUpdate->setId($timer->getId());

        $repository->updateTimer($timer);

        if ($oldTrigger === null) {
            $oldTrigger = $this->getExecutor()
                ->clean()
                ->add('getTrigger', $this, array('id' => $triggerUpdate->getId()))
                ->execute()
                ->getData();
        }

        $options = new CommonEntity(
            array(
                'subEntityName'                  => 'actions',
                'parentIdParameter'              => 'cronTriggerId',
                'createFunction'                 => 'createAction',
                'createFunctionDataParameter'    => 'data',
                'updateFunction'                 => 'updateAction',
                'updateFunctionDataParameter'    => 'data',
                'updateFunctionOldDataParameter' => 'oldAction',
                'deleteFunction'                 => 'deleteAction'
            )
        );

        $this->updateSubEntities($triggerUpdate, $oldTrigger, $actionService, $options);

        return $triggerUpdate;
    }

    /**
     * Deletes cron trigger by given id.
     *
     * @param int                     $id         ID of cron trigger
     * @param Cron\Repository\Trigger $repository Cron trigger repository instance
     *
     * @return int
     */
    public function deleteTrigger($id, Cron\Repository\Trigger $repository) {
        return $repository->deleteTimer($id);
    }
}
