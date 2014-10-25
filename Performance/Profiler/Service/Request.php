<?php

namespace PM\Profiler\Service;

use PM\Main\Abstracts\Service;
use PM\Main\Database;
use PM\Profiler\Repository\Factory;
use PM\Profiler\Entity;

/**
 * This script defines class for request service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Request extends Service {

    /**
     * Creates new request for scenario.
     *
     * @param array                           $requestData   Array with data of request
     * @param int                             $scenarioId    ID of scenario
     * @param \PM\Profiler\Repository\Factory $factory       Repository factory instance
     * @param \PM\Main\Database               $database      Database instance
     * @param \PM\Profiler\Service\Filter     $filterService Filter service instance
     *
     * @return \PM\Profiler\Entity\Request
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function createRequest($requestData, $scenarioId, Factory $factory, Database $database, Filter $filterService) {
        $transaction = $database->getTransaction()->begin('request');
        $repository  = $factory->getRequest(); /* @var $repository \PM\Profiler\Repository\Request */

        try {
            $request = new Entity\Request($requestData);
            $request->set('scenarioId', $scenarioId);

            $repository->createRequest($request);

            $transaction->commit('request');
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        $parameters= array();
        foreach ($request->get('parameters', array()) as $paramsData) {
            $parameters[] = $this->createParameter($paramsData, $request->getId(), $factory, $database);
        }

        $request->set('parameters', $parameters);

        $filters  = array();
        $executor = $this->getExecutor(__FUNCTION__)->clean();
        $executor->getResult()->set('requestId', $request->getId());

        foreach ($request->get('filters', array()) as $requestData) {
            $executor->getResult()->set('filterData', $requestData);
            $filters[] = $executor->add('createFilter', $filterService)->execute()->getData();
            $executor->clean();
        }

        $request->set('filters', $filters);

        return $request;
    }

    /**
     * Updates existed request with new request data.
     *
     * @param array                           $requestData   Array with request data to update
     * @param \PM\Profiler\Repository\Factory $factory       Repository factory instance
     * @param \PM\Main\Database               $database      Database instance
     * @param \PM\Profiler\Service\Filter     $filterService Filter service instance
     *
     * @return \PM\Profiler\Entity\Request
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function updateRequest($requestData, Factory $factory, Database $database, Filter $filterService) {
        $updateRequest = new Entity\Request($requestData);
        $repository    = $factory->getRequest();
        $request       = $this->getRequest($updateRequest->getId(), $factory, true, true, $filterService);
        $transaction   = $database->getTransaction()->begin(__FUNCTION__);

        try {
            $repository->updateRequest($updateRequest);
            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        $this->_updateParams($updateRequest, $request, $factory, $database);
        $this->_updateFilters($updateRequest, $request, $database, $filterService);

        return $this->getRequest($updateRequest->getId(), $factory, true, true, $filterService);;
    }

    /**
     * Updates parameters for request.
     *
     * @param \PM\Profiler\Entity\Request     $updateRequest Request entity instance to update
     * @param \PM\Profiler\Entity\Request     $request       Existed request entity instance
     * @param \PM\Profiler\Repository\Factory $factory       Repository factory instance
     * @param \PM\Main\Database               $database      Database instance
     *
     * @return \PM\Profiler\Service\Request
     *
     * @throws \PM\Profiler\Service\Exception
     */
    private function _updateParams(Entity\Request $updateRequest, Entity\Request $request, Factory $factory, Database $database) {
        $repository    = $factory->getRequest();
        $updateParams  = $updateRequest->get('parameters', array());
        $newParams     = array();
        $existedParams = array();

        foreach ($request->get('parameters', array()) as $parameter) {
            $existedParams[$parameter->getId()] = $parameter;
        }

        foreach ($updateParams as $key => $parameter) {
            $parameter = new Entity\Parameter($parameter);
            $id        = $parameter->get('id', 0);
            $parameter->set('requestId', $request->getId());

            if (array_key_exists($id, $existedParams)) {
                unset($existedParams[$id]);
                $updateParams[$key] = $parameter;
            } else {
                $newParams[] = $parameter;
                unset($updateParams[$key]);
            }
        }

        $transaction = $database->getTransaction()->begin(__FUNCTION__);
        try {
            foreach ($newParams as $parameter) {
                $repository->createParameter($parameter);
            }

            foreach ($updateParams as $parameter) {
                $repository->updateParameter($parameter);
            }

            foreach ($existedParams as $parameter) {
                $repository->deleteParameter($parameter->getId());
            }

            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return $this;
    }

    /**
     * Updates filters for request.
     *
     * @param \PM\Profiler\Entity\Request $updateRequest Request entity instance to update
     * @param \PM\Profiler\Entity\Request $request       Existed request entity instance
     * @param \PM\Main\Database           $database      Database instance
     * @param \PM\Profiler\Service\Filter $filterService Filter service instance
     *
     * @return \PM\Profiler\Service\Request
     *
     * @throws \PM\Profiler\Service\Exception
     */
    private function _updateFilters(Entity\Request $updateRequest, Entity\Request $request, Database $database, Filter $filterService) {
        $updateFilters  = $updateRequest->get('filters', array());
        $newFilters     = array();
        $existedFilters = array();

        foreach ($request->get('filters', array()) as $filter) {
            $existedFilters[$filter->getId()] = $filter;
        }

        foreach ($updateFilters as $key => $filter) {
            $filter = new Entity\Filter($filter);
            $id     = $filter->get('id', 0);

            if (array_key_exists($id, $existedFilters )) {
                unset($existedFilters [$id]);
                $updateFilters[$key] = $filter;
            } else {
                $newFilters[] = $filter;
                unset($updateFilters[$key]);
            }
        }

        try {
            $executor = $this->getExecutor(__FUNCTION__)->clean();
            $executor->getResult()->set('requestId', $request->getId());
            foreach ($newFilters as $filter) {
                $executor->getResult()->set('filterData', $filter->toArray());
                $executor->add('createFilter', $filterService)->execute();
                $executor->clean();
            }

            foreach ($updateFilters as $filter) {
                $executor->getResult()->set('filterData', $filter->toArray());
                $executor->add('updateFilter', $filterService)->execute();
                $executor->clean();
            }

            foreach ($existedFilters as $filter) {
                $executor->getResult()->set('id', $filter->getId());
                $executor->add('deleteFilter', $filterService)->execute();
                $executor->clean();
            }
        } catch (Database\Exception $exc) {
            $database->getTransaction()->rollBack();
            throw $exc;
        }

        return $this;
    }

    /**
     * Deletes request in database.
     *
     * @param int                             $id       ID of request
     * @param \PM\Profiler\Repository\Factory $factory  Repository factory instance
     * @param \PM\Main\Database               $database Database instance
     *
     * @return boolean
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function deleteRequest($id, Factory $factory, Database $database) {
        $transaction = $database->getTransaction()->begin(__FUNCTION__);
        $repository  = $factory->getRequest();

        try {
            $repository->deleteRequest($id);
            $transaction->commit(__FUNCTION__);
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return true;
    }

    /**
     * Creates new parameter for request.
     *
     * @param array                           $parameterData Array with data for new parameter
     * @param int                             $requestId     ID of request
     * @param \PM\Profiler\Repository\Factory $factory       Repository factory instance
     * @param \PM\Main\Database               $database      Database instance
     *
     * @return \PM\Profiler\Entity\Parameter
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function createParameter($parameterData, $requestId, Factory $factory, Database $database) {
        $transaction = $database->getTransaction()->begin('parameter');
        $repository  = $factory->getRequest(); /* @var $repository \PM\Profiler\Repository\Request */

        try {
            $parameter = new Entity\Parameter($parameterData);
            $parameter->set('requestId', $requestId);

            $repository->createParameter($parameter);

            $transaction->commit('parameter');
        } catch (Database\Exception $exc) {
            $transaction->rollBack();
            throw $exc;
        }

        return $parameter;
    }

    /**
     * Returns request with additional data.
     *
     * @param int                             $id             ID of request
     * @param \PM\Profiler\Repository\Factory $factory        Repository factory instance
     * @param boolean                         $includeParams  Include assigned parameters
     * @param boolean                         $includeFilters Include assigned filters
     * @param \PM\Profiler\Service\Filter     $filterService  Filter service instance
     *
     * @return \PM\Profiler\Entity\Request
     */
    public function getRequest($id, Factory $factory, $includeParams = false, $includeFilters = false, Filter $filterService = null) {
        $repository = $factory->getRequest();
        $request    = $repository->getRequest($id);

        if ($includeParams === true && !empty($request)) {
            $params = $repository->getParamsForRequests($id);
            $request->set('parameters', $params);
        }

        if ($includeFilters === true && !empty($request)) {
            $executor = $this->getExecutor(__FUNCTION__);
            $executor->getResult()->set('requestsIds', $id);
            $filters = $executor->add('getFiltersForRequests', $filterService)->execute()->getData();
            $request->set('filters', $filters);
        }

        return $request;
    }

    /**
     * Returns list of requests for scenario.
     *
     * @param int                             $scenarioId     ID of scenario
     * @param \PM\Profiler\Repository\Factory $factory        Repository factory instance
     * @param boolean                         $includeParams  Include assigned parameters
     * @param boolean                         $includeFilters Include assigned filters
     * @param \PM\Profiler\Service\Filter     $filterService  Filter service instance
     *
     * @return type
     */
    public function getRequestsForScenario($scenarioId, Factory $factory, $includeParams = false, $includeFilters = false, Filter $filterService = null) {
        $repository = $factory->getRequest();
        $requests   = $repository->getRequestsForScenario($scenarioId);

        if ($includeParams === true && !empty($requests)) {
            $params = $repository->getParamsForRequests(array_keys($requests));
            foreach($params as $param) {
                $request     = $requests[$param->get('requestId')]; /* @var $request \PM\Profiler\Entity\Request */
                $reqParams   = $request->get('parameters', array());
                $reqParams[] = $param;
                $request->set('parameters', $reqParams);
            }
        }

        if ($includeFilters === true && !empty($requests)) {
            $executor = $this->getExecutor(__FUNCTION__);
            $executor->getResult()->set('requestsIds', array_keys($requests));
            $filters = $executor->add('getFiltersForRequests', $filterService)->execute()->getData();

            foreach($filters as $filter) {
                $request      = $requests[$filter->get('requestId')]; /* @var $request \PM\Profiler\Entity\Request */
                $reqFilters   = $request->get('filters', array());
                $reqFilters[] = $filter;
                $request->set('filters', $reqFilters);
            }
        }

        return array_values($requests);
    }
}
