<?php

namespace PM\Statistic\Service;

use PM\Main\Abstracts\Service;
use PM\Statistic\Entity;
use PM\Statistic\Repository;

/**
 * This script defines class for statistic service of statistic data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Statistic extends Service {

    /**
     * Creates new statistic data entity.
     *
     * @param array          $statistic  Statistic data
     * @param Repository\Run $repository Run repository instance
     *
     * @return Entity\Statistic
     */
    public function createStatistic($statistic, Repository\Run $repository) {
        $statisticEntity = new Entity\Statistic($statistic);

        $repository->createStatistic($statisticEntity);

        return $statisticEntity->getId();
    }
}
