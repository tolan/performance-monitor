<?php

namespace PF\Search\Service;

use PF\Main\Abstracts;
use PF\Search\Repository;
use PF\Search\Entity;

/**
 * This script defines class for template service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Template extends Abstracts\Service {

    /**
     * Find templates for given usage.
     *
     * @param \PF\Search\Repository\Template $repository Template repository instance
     * @param enum                           $usage      One of enum \PF\Search\Enum\Usage
     *
     * @return \PF\Search\Entity\Template[]
     */
    public function findTemplates(Repository\Template $repository, $usage = null) {
        return $repository->findTemplates($usage);
    }

    /**
     * Gets template by given ID.
     *
     * @param int                            $id           ID of template
     * @param \PF\Search\Repository\Template $repository   Template repository instance
     * @param \PF\Search\Service\Group       $groupService Group service instance
     *
     * @return \PF\Search\Entity\Template
     */
    public function getTemplate($id, Repository\Template $repository, Group $groupService) {
        $template = $repository->getTemplate($id);

        $executor = $this->getExecutor()->add('getGroupsForTemplate', $groupService);
        $executor->getResult()->set('templateId', $id);

        $groups = $executor->execute()->getData();

        $template->set('groups', $groups);

        return $template;
    }

    /**
     * Creates new template of search.
     *
     * @param array                          $templateData Data of template for create
     * @param \PF\Search\Repository\Template $repository   Template repository instance
     * @param \PF\Search\Service\Group       $groupService Group service instance
     *
     * @return \PF\Search\Entity\Template
     */
    public function createTemplate($templateData, Repository\Template $repository, Group $groupService) {
        $template = new Entity\Template($templateData);

        $repository->createTemplate($template);

        $executor = $this->getExecutor()->add('createGroup', $groupService);

        $groups = array();
        foreach ($template->getGroups() as $group) {
            $group['templateId'] = $template->getId();
            $executor->getResult()->set('groupData', $group);
            $groups[] = $executor->execute()->getData();
        }

        $template->setGroups($groups);

        return $template->getId();
    }

    /**
     * Update template of search.
     *
     * @param array                          $templateData Data of template for update
     * @param \PF\Search\Repository\Template $repository   Template repository instance
     * @param \PF\Search\Service\Group       $groupService Group service instance
     *
     * @return \PF\Search\Entity\Template
     */
    public function updateTemplate($templateData, Repository\Template $repository, Group $groupService) {
        $templateUpdate = new Entity\Template($templateData);

        $executor = $this->getExecutor()->clean();
        $executor->add('getTemplate', $this)->getResult()->setId($templateUpdate->getId());

        $template = $executor->execute()->getData();

        $repository->updateTemplate($templateUpdate);

        $this->_updateGroups($templateUpdate, $template, $groupService);

        return $templateUpdate;
    }

    /**
     * Updates groups sub-entities of template.
     *
     * @param \PF\Search\Entity\Template $templateUpdate Entity of template for update
     * @param \PF\Search\Entity\Template $template       Entity of existed template
     * @param \PF\Search\Service\Group   $groupService   Group service instance
     *
     * @return \PF\Search\Entity\Template
     */
    private function _updateGroups(Entity\Template $templateUpdate, Entity\Template $template, Group $groupService) {
        $existed = array();

        foreach ($template->getGroups() as $group) {
            $existed[$group->getId()] = $group;
        }

        $toCreate = array();
        $toUpdate = array();

        foreach ($templateUpdate->getGroups() as $group) {
            if (array_key_exists('id', $group) === false || array_key_exists($group['id'], $existed) === false) {
                $toCreate[] = $group;
            } elseif(array_key_exists($group['id'], $existed) === true) {
                $toUpdate[$group['id']] = $group;
            }
        }

        $toDelete = array_diff_key($existed, $toUpdate);

        $executor = $this->getExecutor()->clean()->add('createGroup', $groupService);
        foreach ($toCreate as $group) {
            $group['templateId'] = $templateUpdate->getId();
            $executor->getResult()->set('groupData', $group);
            $executor->execute();
        }

        $executor->clean()->add('updateGroup', $groupService);
        foreach ($toUpdate as $group) {
            $executor->getResult()->set('groupData', $group)->set('group', $existed[$group['id']]);
            $executor->execute();
        }

        $executor->clean()->add('deleteGroup', $groupService);
        foreach (array_keys($toDelete) as $groupId) {
            $executor->getResult()->set('id', $groupId);
            $executor->execute();
        }

        return $templateUpdate;
    }

    /**
     * Deletes search template by given ID
     *
     * @param int                            $id         ID of template
     * @param \PF\Search\Repository\Template $repository Template repository instance
     *
     * @return int
     */
    public function deleteTemplate($id, Repository\Template $repository) {
        return $repository->deleteTemplate($id);
    }
}
