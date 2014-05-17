<?php

namespace PF\Profiler\Service;

use PF\Main\Abstracts\Service;
use PF\Main\Database;
use PF\Profiler\Repository\Factory;
use PF\Profiler\Entity;

/**
 * This script defines class for filter service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Filter extends Service {

    /**
     * Create new filter for request.
     *
     * @param array                           $filterData Array with filter data
     * @param \PF\Profiler\Repository\Factory $factory    Repository factory instance
     * @param \PF\Main\Database               $database   Database instance
     * @param int                             $requestId  ID of request
     * @param int                             $setId      ID of set of filters
     *
     * @return \PF\Profiler\Entity\Filter
     *
     * @throws \PF\Profiler\Service\Exception
     */
    public function createFilter($filterData, Factory $factory, Database $database, $requestId = null, $setId = null) {
        $repository  = $factory->getFilter();
        $transaction = $database->getTransaction()->begin('filter');

        try {
            $filter = new Entity\Filter($filterData);
            $filter->set('requestId', $requestId);
            $filter->set('setId', $setId);

            $repository->create($filter);

            if ($requestId) {
                $repository->assign($filter, $requestId, $setId);
            }

            $transaction->commit('filter');
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return $filter;
    }

    /**
     * Updates existed request filter.
     *
     * @param array                           $filterData Filter data to update
     * @param \PF\Profiler\Repository\Factory $factory    Repository factory instance
     * @param \PF\Main\Database               $database   Database instance
     *
     * @return \PF\Profiler\Entity\Filter
     *
     * @throws \PF\Profiler\Service\Exception
     */
    public function updateFilter($filterData, Factory $factory, Database $database) {
        $repository  = $factory->getFilter();
        $transaction = $database->getTransaction()->begin(__FUNCTION__);

        try {
            $filter = new Entity\Filter($filterData);
            $repository->update($filter);
            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return $filter;
    }

    /**
     * Deletes filter by given ID.
     *
     * @param int                             $id       ID of filter
     * @param \PF\Profiler\Repository\Factory $factory  Repository factory instance
     * @param \PF\Main\Database               $database Database instance
     *
     * @return boolean
     *
     * @throws \PF\Profiler\Service\Exception
     */
    public function deleteFilter($id, Factory $factory, Database $database) {
        $repository  = $factory->getFilter();
        $transaction = $database->getTransaction()->begin(__FUNCTION__);

        try {
            $repository->delete($id);
            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return true;
    }

    /**
     * Returns filter entities for requests.
     *
     * @param array                           $requestsIds IDs of requests
     * @param \PF\Profiler\Repository\Factory $factory     Repository factory instance
     *
     * @return array
     */
    public function getFiltersForRequests($requestsIds, Factory $factory) {
        $repository = $factory->getFilter();

        return array_values($repository->getFiltersForRequests($requestsIds));
    }
}
