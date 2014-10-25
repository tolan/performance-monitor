<?php

namespace PM\Profiler\Repository;

use PM\Main\Abstracts\Repository;
use PM\Profiler\Entity;

/**
 * This script defines class for filter repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Filter extends Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('request_filter');
    }

    /**
     * Creates new filter in database.
     *
     * @param \PM\Profiler\Entity\Filter $filter Filter entity instance
     *
     * @return \PM\Profiler\Entity\Filter Filter with new ID
     */
    public function create(Entity\Filter $filter) {
        $data = array(
            'type'       => $filter->get('type'),
            'parameters' => json_encode($filter->get('parameters'))
        );

        $id = parent::create($data);

        $filter->setId($id);

        return $filter;
    }

    /**
     * Updates filter in database.
     *
     * @param \PM\Profiler\Entity\Filter $filter Filter entity instance
     *
     * @return boolean
     */
    public function update(Entity\Filter $filter) {
        $data = array(
            'type'       => $filter->get('type'),
            'parameters' => json_encode($filter->get('parameters'))
        );

        parent::update($filter->getId(), $data);

        return true;
    }

    /**
     * Deletes filter in database by given id.
     *
     * @param int $id ID of filter
     *
     * @return boolean
     */
    public function delete($id) {
        parent::delete($id);

        return true;
    }

    /**
     * Assign filter to request by given request ID, set ID and filter.
     *
     * @param \PM\Profiler\Entity\Filter $filter    Filter entity instance
     * @param int                        $requestId ID of request
     * @param int                        $setId     ID of set of filters
     *
     * @return \PM\Profiler\Entity\Filter
     */
    public function assign(Entity\Filter $filter, $requestId, $setId) {
        $data = array(
            'requestId' => $requestId,
            'filterId'  => $filter->get('id'),
            'setId'     => $setId ? $setId : 0
        );

        parent::create($data, 'request_filter_set');

        return $filter;
    }

    /**
     * Returns list of filters for requests.
     *
     * @param array $requestsIds Array with requests IDs
     *
     * @return \PM\Profiler\Entity\Filter[]
     */
    public function getFiltersForRequests($requestsIds) {
        $select = $this->getDatabase()->select()
            ->from(array('rf' => $this->getTableName()))
            ->joinInner(array('rfs' => 'request_filter_set'), 'rf.id = rfs.filterId', array('requestId'))
            ->where('rfs.requestId IN (?)', $requestsIds);

        $data   = $select->fetchAll();
        $result = array();

        foreach ($data as $filter) {
            $filter['parameters']  = json_decode($filter['parameters'], true);
            $filter['id']          = (int)$filter['id'];
            $filter['requestId']   = (int)$filter['requestId'];

            $result[$filter['id']] = new Entity\Filter($filter);
        }

        return $result;
    }
}
