<?php

namespace PM\Profiler\Repository;

use PM\Main\Abstracts\Repository;
use PM\Profiler\Entity;

/**
 * This script defines class for request repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Request extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('scenario_request');
    }

    /**
     * Create new request in database by given request entity.
     *
     * @param \PM\Profiler\Entity\Request $request Request entity instance
     *
     * @return \PM\Profiler\Entity\Request
     */
    public function createRequest(Entity\Request $request) {
        $data = array(
            'scenarioId' => $request->get('scenarioId'),
            'url'        => $request->get('url'),
            'method'     => $request->get('method'),
            'toMeasure'  => $request->get('toMeasure')
        );

        $id = parent::create($data);

        $request->setId($id);

        return $request;
    }

    /**
     * Updates requet in database.
     *
     * @param \PM\Profiler\Entity\Request $request Request entity instance
     *
     * @return boolean
     */
    public function updateRequest(Entity\Request $request) {
        $data = array(
            'scenarioId' => $request->get('scenarioId'),
            'url'        => $request->get('url'),
            'method'     => $request->get('method'),
            'toMeasure'  => $request->get('toMeasure')
        );

        parent::update($request->getId(), $data);

        return true;
    }

    /**
     * Create new request parameter by given parameter entity.
     *
     * @param \PM\Profiler\Entity\Parameter $parameter Parameter entity instance
     *
     * @return int ID of new parameter
     */
    public function createParameter(Entity\Parameter $parameter) {
        $data = array(
            'requestId' => $parameter->get('requestId'),
            'method'    => $parameter->get('method'),
            'name'      => $parameter->get('name'),
            'value'     => $parameter->get('value', '')
        );

        return parent::create($data, 'request_parameter');
    }

    /**
     * Updates request parameter.
     *
     * @param \PM\Profiler\Entity\Parameter $parameter Request parameter instance
     *
     * @return boolean
     */
    public function updateParameter(Entity\Parameter $parameter) {
        $data = array(
            'requestId' => $parameter->get('requestId'),
            'method'    => $parameter->get('method'),
            'name'      => $parameter->get('name'),
            'value'     => $parameter->get('value', '')
        );

        parent::update($parameter->getId(), $data, 'request_parameter');

        return true;
    }

    /**
     * Deletes request parameter by given ID.
     *
     * @param int $id ID of request parameter
     *
     * @return boolean
     */
    public function deleteParameter($id) {
        parent::delete($id, 'request_parameter');

        return true;
    }

    /**
     * Returns request entity instance by given ID.
     *
     * @param int $id ID of request
     *
     * @return \PM\Profiler\Entity\Request
     */
    public function getRequest($id) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('id = ?', $id);

        $data = $select->fetchOne();
        $data['toMeasure'] = $data['toMeasure'] === '1' ? true : false;

        $request = new Entity\Request($data);

        return $request;
    }

    /**
     * Returns list of requests for scenario.
     *
     * @param int $scenarioId ID of scenario
     *
     * @return \PM\Profiler\Entity\Request
     */
    public function getRequestsForScenario($scenarioId) {
        $select = $this->getDatabase()->select()
            ->from($this->getTableName())
            ->where('scenarioId = ?', $scenarioId);

        $data = $select->fetchAll();
        $result = array();

        foreach ($data as $request) {
            $request['id']         = (int)$request['id'];
            $request['scenarioId'] = (int)$request['scenarioId'];
            $request['toMeasure']  = $request['toMeasure'] === '1' ? true : false;

            $result[$request['id']] = new Entity\Request($request);
        }

        return $result;
    }

    /**
     * Returns list of request parameters for requests.
     *
     * @param array $requestsIds Array with request IDs
     * 
     * @return \PM\Profiler\Entity\Parameter
     */
    public function getParamsForRequests($requestsIds) {
        $select = $this->getDatabase()->select()
            ->from('request_parameter')
            ->where('requestId IN (?)', $requestsIds);

        $data   = $select->fetchAll();
        $result = array();

        foreach ($data as $parameter) {
            $parameter['id']        = (int)$parameter['id'];
            $parameter['requestId'] = (int)$parameter['requestId'];

            $result[$parameter['id']] = new Entity\Parameter($parameter);
        }

        return $result;
    }
}
