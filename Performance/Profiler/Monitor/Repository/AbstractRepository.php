<?php

namespace PM\Profiler\Monitor\Repository;

use PM\Main\Abstracts\Repository;
use PM\Profiler\Monitor;
use PM\Profiler\Repository\Filter as RepositoryFilter;

/**
 * This script defines abstract class for monitor repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractRepository extends Repository implements Monitor\Interfaces\Repository {

    /**
     * Cache for loaded filters.
     *
     * @var array
     */
    private $_filters = array();

    /**
     * Sets monitor filter repository.
     *
     * @param \PM\Profiler\Repository\Filter $repository Monitor filter repository instance
     * @param int                            $requestId  Request ID
     *
     * @return \PM\Profiler\Monitor\Repository\AbstractRepository
     */
    public function setFilterRepository(RepositoryFilter $repository, $requestId = null) {
        if ($requestId !== null) {
            $this->_filters = $repository->getFiltersForRequests($requestId);
        }

        return $this;
    }

    /**
     * Loads and returns list of filter for request.
     *
     * @return array
     */
    public function loadFilters() {
        return $this->_filters;
    }
}
