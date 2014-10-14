<?php

namespace PF\Profiler\Service;

use PF\Main\Abstracts\Service;
use PF\Main\Database;
use PF\Profiler\Repository;
use PF\Profiler\Monitor\Enum\Type;
use PF\Profiler\Entity;

/**
 * This script defines class for measure service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Measure extends Service {

    /**
     * Returns measure entity by id.
     *
     * @param int                $measureId ID of measure
     * @param Repository\Factory $factory   Repository factory instance
     * @param enum               $type      One of PF\Profiler\Monitor\Enum\Type
     *
     * @return Entity\Measure
     */
    public function getMeasure($measureId, Repository\Factory $factory, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type); /* @var $repository \PF\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasure($measureId);
    }

    /**
     * Creates new measure to MySQL database.
     *
     * @param array                           $measureData Array with measure data
     * @param \PF\Profiler\Repository\Factory $factory     Repository factory instance
     * @param \PF\Main\Database               $database    Database instance
     *
     * @return \PF\Profiler\Entity\Measure
     *
     * @throws \PF\Profiler\Service\Exception
     */
    public function createMySQLMeasure($measureData, Repository\Factory $factory, Database $database) {
        $transaction = $database->getTransaction()->begin(__FUNCTION__);
        $repository = $factory->getMeasure(Type::MYSQL); /* @var $repository \PF\Profiler\Repository\Interfaces\Measure */

        try {
            $measure = new Entity\Measure($measureData);
            $repository->createMeasure($measure);
            $transaction->commit(__FUNCTION__);
        } catch (Exception $ex) {
            $transaction->rollBack();
            throw $ex;
        }

        return $measure;
    }

    /**
     * Returns list of all existed measures in file repository.
     *
     * @param \PF\Profiler\Repository\Factory $factory Repository factory instance
     *
     * @return array
     */
    public function findFileMeasures(Repository\Factory $factory) {
        $repository = $factory->getMeasure(Type::FILE);

        return $repository->findAll();
    }

    /**
     * Returns all measure for test from MySQL.
     *
     * @param int                             $testId  ID of test
     * @param \PF\Profiler\Repository\Factory $factory Repository factory instance
     *
     * @return array
     */
    public function findMeasuresForTest($testId, Repository\Factory $factory) {
        $repository = $factory->getMeasure(Type::MYSQL); /* @var $repository \PF\Profiler\Repository\Interfaces\Measure */

        return $repository->findMeasuresForTest($testId);
    }

    /**
     * Gets statistics information for measure.
     *
     * @param int                             $measureId ID of meassure
     * @param \PF\Profiler\Repository\Factory $factory   Repository factory instance
     * @param enum                            $type      One of PF\Profiler\Monitor\Enum\Type
     *
     * @return array
     */
    public function getMeasureStatistics($measureId, Repository\Factory $factory, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type, $measureId); /* @var $repository \PF\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasureStatistics($measureId);
    }

    /**
     * Gets list of calls for measure and their parent ID.
     *
     * @param int                             $measureId ID of measure
     * @param \PF\Profiler\Repository\Factory $factory   Repository factory instance
     * @param int                             $parentId  ID of parent call (default 0)
     * @param enum                            $type      One of PF\Profiler\Monitor\Enum\Type
     *
     * @return array
     */
    public function getMeasureCallStack($measureId, Repository\Factory $factory, $parentId = 0, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type, $measureId); /* @var $repository \PF\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasureCallStack($measureId, $parentId);
    }

    /**
     * Gets list of statistics for each call by given measure ID.
     *
     * @param int                             $measureId ID of measure
     * @param \PF\Profiler\Repository\Factory $factory   Repository factory instance
     * @param enum                            $type      One of PF\Profiler\Monitor\Enum\Type
     *
     * @return array
     */
    public function getMeasureCallsStatistic($measureId, Repository\Factory $factory, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type, $measureId); /* @var $repository \PF\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasureCallsStatistic($measureId);
    }

    /**
     * Delete measure stored in file.
     *
     * @param int                             $measureId ID of measure
     * @param \PF\Profiler\Repository\Factory $factory   Repository factory instance
     *
     * @return boolean
     */
    public function deleteFileMeasure($measureId, Repository\Factory $factory) {
        $repository = $factory->getMeasure(Type::FILE, $measureId);
        /* @var $repository \PF\Profiler\Repository\Measure\Cache */

        return $repository->deleteMeasure($measureId);
    }
}
