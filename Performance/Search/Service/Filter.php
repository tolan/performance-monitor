<?php

namespace PF\Search\Service;

use PF\Main\Abstracts;
use PF\Search\Repository;
use PF\Search\Entity;

/**
 * This script defines class for filter service of search template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Filter extends Abstracts\Service {

    /**
     * Returns filter instancies for groups and grouped by group ID.
     *
     * @param array                        $groupIds   List of group IDs
     * @param \PF\Search\Repository\Filter $repository Filter repository instance
     *
     * @return \PF\Search\Entity\Filter[]
     */
    public function getFiltersForGroups($groupIds, Repository\Filter $repository) {
        $filters = $repository->getFiltersForGroups($groupIds);
        $result  = array();

        foreach ($filters as $filter) {
            if (array_key_exists($filter->getGroupId(), $result) === false) {
                $result[$filter->getGroupId()] = array();
            }

            $result[$filter->getGroupId()][] = $filter;
        }

        return $result;
    }

    /**
     * Creates new filter by given filter data.
     *
     * @param array                        $filterData Data of new filter
     * @param \PF\Search\Repository\Filter $repository Filter repository instance
     *
     * @return \PF\Search\Entity\Filter
     */
    public function createFilter($filterData, Repository\Filter $repository) {
        $filter = new Entity\Filter($filterData);

        $repository->createFilter($filter);

        return $filter;
    }

    /**
     * Updates fitler with fitler data.
     *
     * @param array                        $filterData Data of filter for update
     * @param \PF\Search\Repository\Filter $repository Filter repository instance
     *
     * @return \PF\Search\Entity\Filter
     */
    public function updateFilter($filterData, Repository\Filter $repository) {
        $filter = new Entity\Filter($filterData);

        $repository->updateFilter($filter);

        return $filter;
    }

    /**
     * Deletes fitler by given ID.
     *
     * @param int                          $id         ID of filter
     * @param \PF\Search\Repository\Filter $repository Fitler repository instance
     * 
     * @return int
     */
    public function deleteFilter($id, Repository\Filter $repository) {
        return $repository->deleteFilter($id);
    }
}
