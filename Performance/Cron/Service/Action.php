<?php

namespace PM\Cron\Service;

use PM\Main\Abstracts;
use PM\Cron;
use PM\Search;
use PM\Main\CommonEntity;

/**
 * This script defines class for action service of cron trigger.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Action extends Abstracts\Service {

    /**
     * It returns actions for cron triggers.
     *
     * @param array                   $triggersIds       Set of triggers ids
     * @param Cron\Repository\Action  $repository        Action repository instance
     * @param Search\Service\Template $searchService     Search service instance
     * @param Cron\Service\ActionTask $actionTaskService Action task service instance
     *
     * @return Cron\Entity\Action[]
     */
    public function getActionsForTriggers(
        $triggersIds, Cron\Repository\Action $repository, Search\Service\Template $searchService, Cron\Service\ActionTask $actionTaskService
    ) {
        $actions = $repository->getActionsForTriggers($triggersIds);

        foreach ($actions as $action) {
            $this->_assignSource($action, $repository, $searchService);
            $tasks = $this->getExecutor()
                ->add('getTasksForAction', $actionTaskService, array('actionId' => $action->getId()))
                ->execute()
                ->getData();
            $action->setTasks($tasks);
        }

        return $actions;
    }

    /**
     * Creates new cron task action by given data.
     *
     * @param array                   $data          Data of new task action
     * @param Cron\Repository\Action  $repository    Task repository instance
     * @param Search\Service\Template $searchService Search service instance
     *
     * @return Cron\Entity\Task
     */
    public function createAction(
        $data, Cron\Repository\Action $repository, Search\Service\Template $searchService, Cron\Service\ActionTask $actionTaskService
    ) {
        $action = new Cron\Entity\Action($data);

        $source         = $action->getSource();
        $searchTemplate = $source['template'];

        if (!isset($searchTemplate['id'])) {
            $executor = $this->getExecutor()->add('createTemplate', $searchService);
            $searchTemplate['visible'] = false;
            $searchTemplate['usage']   = Search\Enum\Usage::CRON;
            $searchTemplate['name']    = 'cron hidden';
            $executor->getResult()->set('templateData', $searchTemplate);
            $searchTemplate['id'] = $executor->execute()->get('data');
        }

        $source['template'] = $searchTemplate;
        $action->set('source', $source);

        $action->setSourceType($source['type']);
        $action->setSourceTemplateId($searchTemplate['id']);

        $repository->createAction($action);

        if ($source['type'] !== Cron\Enum\Source::TEMPLATE) {
            $repository->assignItemsToAction($action->getId(), $source['items']);
        }

        $executor = $this->getExecutor()->add('createActionTask', $actionTaskService);

        $tasks = array();
        foreach ($action->getTasks() as $task) {
            $task['cronTriggerSourceId'] = $action->getId();
            $executor->getResult()->set('data', $task);
            $tasks[] = $executor->execute()->getData();
        }

        $action->setTasks($tasks);

        return $action;
    }

    /**
     * Updates existed cron task action by given data.
     *
     * @param array                   $data              Data of existed action
     * @param Cron\Repository\Action  $repository        Action repository instance
     * @param Search\Service\Template $searchService     Search service instance
     * @param Cron\Service\ActionTask $actionTaskService Action task service instance
     * @param Cron\Entity\Action      $oldAction         Cron entity action with old data
     *
     * @return Cron\Entity\Action
     */
    public function updateAction(
        $data, Cron\Repository\Action $repository, Search\Service\Template $searchService, Cron\Service\ActionTask $actionTaskService,
        Cron\Entity\Action $oldAction = null
    ) {
        $actionUpdate = new Cron\Entity\Action($data);

        $source         = $actionUpdate->get('source');
        $searchTemplate = $source['template'];

        if (!isset($searchTemplate['id'])) {
            $executor = $this->getExecutor()->add('createTemplate', $searchService);
            $searchTemplate['visible'] = false;
            $searchTemplate['usage']   = Search\Enum\Usage::CRON;
            $searchTemplate['name']    = 'cron hidden';
            $executor->getResult()->set('templateData', $searchTemplate);
            $searchTemplate['id'] = $executor->execute()->get('data');
        }

        $source['template'] = $searchTemplate;
        $actionUpdate->set('source', $source);
        $actionUpdate->set('sourceType', $source['type']);

        $repository->updateAction($actionUpdate);

        if ($source['type'] !== Cron\Enum\Source::TEMPLATE) {
            $repository->deleteItemsToAction($actionUpdate->getId());
            $repository->assignItemsToAction($actionUpdate->getId(), $source['items']);
        }

        if ($oldAction === null) {
            $oldAction = $this->getExecutor()
                ->clean()
                ->add('getAction', $this, array('id' => $actionUpdate->getId()))
                ->execute()
                ->getData();
        }

        $options = new CommonEntity(
            array(
                'subEntityName'                  => 'tasks',
                'parentIdParameter'              => 'cronTriggerSourceId',
                'createFunction'                 => 'createActionTask',
                'createFunctionDataParameter'    => 'data',
                'updateFunction'                 => 'updateActionTask',
                'updateFunctionDataParameter'    => 'data',
                'updateFunctionOldDataParameter' => 'oldActionTask',
                'deleteFunction'                 => 'deleteActionTask'
            )
        );

        $this->updateSubEntities($actionUpdate, $oldAction, $actionTaskService, $options);

        return $actionUpdate;
    }

    /**
     * Deletes cron task action entity.
     *
     * @param int                    $id         Id of cron task action entity
     * @param Cron\Repository\Action $repository Action repository instance
     *
     * @return int
     */
    public function deleteAction($id, Cron\Repository\Action $repository) {
        return $repository->deleteAction($id);
    }

    /**
     * It assigns source to cron task action.
     *
     * @param Cron\Entity\Action      $action        Cron task action entity instance
     * @param Cron\Repository\Action  $repository    Action repository instance
     * @param Search\Service\Template $searchService Search service instance
     *
     * @return Action
     */
    private function _assignSource(Cron\Entity\Action $action, Cron\Repository\Action $repository, Search\Service\Template $searchService) {
        $source         = array();
        $searchTemplate = $this->getExecutor()
                ->add('getTemplate', $searchService, array('id' => $action->getSourceTemplateId()))
                ->execute()
                ->getData();

        $source['template'] = $searchTemplate;
        $source['type']     = $action->getSourceType();
        $source['target']   = $searchTemplate->getTarget();
        $action->reset('sourceType');

        $source['items'] = array();
        if ($source['type'] !== Cron\Enum\Source::TEMPLATE) {
            $source['items'] = $repository->getItemsForAction($action->getId());
        }

        $action->setSource($source);

        return $this;
    }
}
