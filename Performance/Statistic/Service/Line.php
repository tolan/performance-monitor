<?php

namespace PF\Statistic\Service;

use PF\Main\Abstracts\Service;
use PF\Statistic\Repository;
use PF\Statistic\Entity;

/**
 * This script defines class for line service of statistic template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Line extends Service {

    /**
     * Returns list of line entities for view IDs.
     *
     * @param array                         $viewIds    List of view IDs
     * @param \PF\Statistic\Repository\Line $repository Line repository instance
     *
     * @return \PF\Statistic\Entity\Line[]
     */
    public function getLinesForViews($viewIds, Repository\Line $repository) {
        return $repository->getLinesForViews($viewIds);
    }

    /**
     * Creates new line entity.
     *
     * @param array                         $lineData   Data of line for create
     * @param \PF\Statistic\Repository\Line $repository Line repository instance
     *
     * @return \PF\Statistic\Entity\Line
     */
    public function createLine($lineData, Repository\Line $repository) {
        $line = new Entity\Line($lineData);

        $repository->createLine($line);

        return $line;
    }

    /**
     * Updates line entity.
     *
     * @param array                         $lineData   Data of line for create
     * @param \PF\Statistic\Repository\Line $repository Line repository instance
     *
     * @return \PF\Statistic\Entity\Line
     */
    public function updateLine($lineData, Repository\Line $repository) {
        $line = new Entity\Line($lineData);

        $repository->updateLine($line);

        return $line;
    }

    /**
     * Deletes line entity by given ID.
     *
     * @param int                           $id         ID of line entity.
     * @param \PF\Statistic\Repository\Line $repository Line repository instance
     *
     * @return int
     */
    public function deleteLine($id, Repository\Line $repository) {
        return $repository->deleteLine($id);
    }
}
