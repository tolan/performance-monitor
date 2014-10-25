<?php

namespace PM\Profiler\Repository\Interfaces;

use PM\Profiler\Entity;

/**
 * Interface for measure repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Measure {

    /**
     * Creates new measure in repository with basic data.
     *
     * @param \PM\Profiler\Entity\Measure $measure Measure entity instance
     */
    public function createMeasure(Entity\Measure $measure);

    /**
     * Get measure statistics like as count of calls, consumed time, date of start, etc.
     *
     * @param int $measureId ID of measure
     */
    public function getMeasureStatistics($measureId);

    /**
     * Get list of calls by given measure ID and ID of parent call.
     *
     * @param int $measureId ID of measure
     * @param int $parentId  ID of parent call (default 0 it means get root calls)
     */
    public function getMeasureCallStack($measureId, $parentId = 0);

    /**
     * Get list statistics information about calls grouped by file and line.
     *
     * @param int $measureId ID of measure
     */
    public function getMeasureCallsStatistic($measureId);

    /**
     * Returns measure entity.
     *
     * @param int $measureId Id of measure
     */
    public function getMeasure($measureId);
}
