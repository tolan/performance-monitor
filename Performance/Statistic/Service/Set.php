<?php

namespace PF\Statistic\Service;

use PF\Statistic\Repository;
use PF\Statistic\Entity;
use PF\Main\Abstracts\Service;

/**
 * This script defines class for set service of statistic data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Set extends Service {

    /**
     * Returns list of sets entities.
     *
     * @param \PF\Statistic\Repository\Set $repository Set repository instance
     *
     * @return \PF\Statistic\Entity\Set[]
     */
    public function findSets(Repository\Set $repository) {
        $data = $repository->findSets();

        return $data;
    }

    /**
     * Returns statistic set entity by given id.
     *
     * @param int            $id          Id of statistic set
     * @param Repository\Set $repository  Set repository instance
     * @param Run            $runService  Run service instance
     * @param boolean        $includeRuns Flag for adding runs
     *
     * @return Entity\Set
     */
    public function getSet($id, Repository\Set $repository, Run $runService, $includeRuns = false) {
        $set = $repository->getSet($id);

        $set->setTemplates($repository->getTemplates($id));

        if ($includeRuns === true) {
            $executor = $this->getExecutor()->add('getRunsForSet', $runService);
            $executor->getResult()->setSetId($id);
            $runs = $executor->execute()->getData();

            $set->setRuns($runs);
        }

        return $set;
    }

    /**
     * Creates new statistic set and assing templates.
     *
     * @param array          $setData    Statistic set data
     * @param Repository\Set $repository Set repository instance
     *
     * @return Entity\Set
     */
    public function createSet($setData, Repository\Set $repository) {
        $set = new Entity\Set($setData);

        $repository->createSet($set);

        $repository->assignTemplates($set->getId(), $set->getTemplates());

        return $set;
    }

    /**
     * Updates statistic set entity and reassign all templates.
     *
     * @param array          $setData    Statistic set data
     * @param Repository\Set $repository Set repository instance
     *
     * @return Entity\Set
     */
    public function updateSet($setData, Repository\Set $repository) {
        $set = new Entity\Set($setData);

        $repository->updateSet($set);

        $repository->deleteTemplates($set->getId());
        $repository->assignTemplates($set->getId(), $set->getTemplates());

        return $set;
    }

    /**
     * Deletes statistic set entity by given ID.
     *
     * @param type           $id         Id of statistic set
     * @param Repository\Set $repository Set repository instance
     *
     * @return int
     */
    public function deleteSet($id, Repository\Set $repository) {
        return $repository->deleteSet($id);
    }
}
