<?php

namespace PF\Search\Service;

use PF\Main\Abstracts;
use PF\Search\Repository;
use PF\Search\Entity;

/**
 * This script defines class for group service of search template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Group extends Abstracts\Service {

    /**
     * Returns groups of search template (include all filters).
     *
     * @param int                         $templateId    ID of template
     * @param \PF\Search\Repository\Group $repository    Group repository instance
     * @param \PF\Search\Service\Filter   $filterService Filter service instance
     *
     * @return \PF\Search\Entity\Group[]
     */
    public function getGroupsForTemplate($templateId, Repository\Group $repository, Filter $filterService) {
        $groups = $repository->getGroupsForTemplate($templateId);

        $gIds = array();
        foreach ($groups as $group) {
            $gIds[] = $group->getId();
        }

        $executor = $this->getExecutor()->clean()->add('getFiltersForGroups', $filterService);
        $executor->getResult()->set('groupIds', $gIds);

        $filters = $executor->execute();

        foreach ($groups as $group) {
            $group->setFilters($filters->get($group->getId()));
        }

        return $groups;
    }

    /**
     * Creates new group of search template.
     *
     * @param array                       $groupData     Data of new group
     * @param \PF\Search\Repository\Group $repository    Group repository instance
     * @param \PF\Search\Service\Filter   $filterService Filter service instance
     *
     * @return \PF\Search\Entity\Group
     */
    public function createGroup($groupData, Repository\Group $repository, Filter $filterService) {
        $group = new Entity\Group($groupData);

        $repository->createGroup($group);

        $executor = $this->getExecutor()->clean()->add('createFilter', $filterService);

        $filters = array();
        foreach ($group->getFilters() as $filter) {
            $filter['groupId'] = $group->getId();
            $executor->getResult()->set('filterData', $filter);
            $filters[] = $executor->execute()->getData();
        }

        $group->setFilters($filters);

        return $group;
    }

    /**
     * Updates group of search template.
     *
     * @param array                       $groupData     Data of group for update
     * @param \PF\Search\Entity\Group     $group         Entity of existed group entity
     * @param \PF\Search\Repository\Group $repository    Group repository instance
     * @param \PF\Search\Service\Filter   $filterService Filter service instance
     *
     * @return \PF\Search\Entity\Group
     */
    public function updateGroup($groupData, Entity\Group $group, Repository\Group $repository, Filter $filterService) {
        if ($group === null) {
            $group =  $repository->getGroup($groupData['id']);
        }

        $groupUpdate = new Entity\Group($groupData);

        $repository->updateGroup($groupUpdate);

        $this->_updateFilters($groupUpdate, $group, $filterService);

        return $groupUpdate;
    }

    /**
     * Updates filters sub-entities of group.
     *
     * @param \PF\Search\Entity\Group   $groupUpdate   Entity of group for update
     * @param \PF\Search\Entity\Group   $group         Entity of existed group
     * @param \PF\Search\Service\Filter $filterService Filter service instance
     *
     * @return \PF\Search\Entity\Group
     */
    private function _updateFilters(Entity\Group $groupUpdate, Entity\Group $group, Filter $filterService) {
        $existed = array();

        foreach ($group->getFilters() as $filter) {
            $existed[$filter->getId()] = $filter;
        }

        $toCreate = array();
        $toUpdate = array();

        foreach ($groupUpdate->getFilters() as $filter) {
            if (array_key_exists('id', $filter) === false || array_key_exists($filter['id'], $existed) === false) {
                $toCreate[] = $filter;
            } elseif(array_key_exists($filter['id'], $existed) === true) {
                $toUpdate[$filter['id']] = $filter;
            }
        }

        $toDelete = array_diff_key($existed, $toUpdate);

        $executor = $this->getExecutor()->clean()->add('createFilter', $filterService);
        foreach ($toCreate as $filter) {
            $filter['groupId'] = $groupUpdate->getId();
            $executor->getResult()->set('filterData', $filter);
            $executor->execute();
        }

        $executor->clean()->add('updateFilter', $filterService);
        foreach ($toUpdate as $filter) {
            $executor->getResult()->set('filterData', $filter);
            $executor->execute();
        }

        $executor->clean()->add('deleteFilter', $filterService);
        foreach (array_keys($toDelete) as $filterId) {
            $executor->getResult()->set('id', $filterId);
            $executor->execute();
        }

        return $groupUpdate;
    }

    /**
     * Deletes search template group by given ID.
     *
     * @param int                         $id         ID of group
     * @param \PF\Search\Repository\Group $repository Group repository instance
     *
     * @return int
     */
    public function deleteGroup($id, Repository\Group $repository) {
        return $repository->deleteGroup($id);
    }
}
