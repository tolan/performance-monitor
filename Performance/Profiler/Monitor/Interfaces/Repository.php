<?php

namespace PF\Profiler\Monitor\Interfaces;

use PF\Main\Interfaces\Observer;
use PF\Profiler\Repository\Filter as RepositoryFilter;

/**
 * Interface for monitor analyzator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Repository extends Observer {

    /**
     * Loads filter entities.
     *
     * @return array List of filter entities
     */
    public function loadFilters();

    /**
     * Sets repository for handling filter entities.
     *
     * @param \PF\Profiler\Repository\Filter $repository Filter repository instance
     * @param int                            $requestId  Request ID
     */
    public function setFilterRepository(RepositoryFilter $repository, $requestId = null);

    /**
     * Saves statistics of call stack.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     */
    public function saveCallStatistics(Storage $storage);

    /**
     * Loads call statistics into storage.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     */
    public function loadCallStatistics(Storage $storage);

    /**
     * Saves call fly weight which has hash table with contents and lines.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Call $call Call fly weight instance
     */
    public function saveCallFlyweight(Call $call);

    /**
     * Resets all data in caches and stored information.
     */
    public function reset();
}
