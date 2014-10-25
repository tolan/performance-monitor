<?php

namespace PM\Profiler\Service;

use PM\Main\Abstracts\Service;
use PM\Main\Database;
use PM\Profiler\Repository;
use PM\Profiler\Monitor\Enum\Type;
use PM\Profiler\Entity;

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
     * @param enum               $type      One of PM\Profiler\Monitor\Enum\Type
     *
     * @return Entity\Measure
     */
    public function getMeasure($measureId, Repository\Factory $factory, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type); /* @var $repository \PM\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasure($measureId);
    }

    /**
     * Creates new measure to MySQL database.
     *
     * @param array                           $measureData Array with measure data
     * @param \PM\Profiler\Repository\Factory $factory     Repository factory instance
     * @param \PM\Main\Database               $database    Database instance
     *
     * @return \PM\Profiler\Entity\Measure
     *
     * @throws \PM\Profiler\Service\Exception
     */
    public function createMySQLMeasure($measureData, Repository\Factory $factory, Database $database) {
        $transaction = $database->getTransaction()->begin(__FUNCTION__);
        $repository = $factory->getMeasure(Type::MYSQL); /* @var $repository \PM\Profiler\Repository\Interfaces\Measure */

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
     * @param \PM\Profiler\Repository\Factory $factory Repository factory instance
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
     * @param \PM\Profiler\Repository\Factory $factory Repository factory instance
     *
     * @return array
     */
    public function findMeasuresForTest($testId, Repository\Factory $factory) {
        $repository = $factory->getMeasure(Type::MYSQL); /* @var $repository \PM\Profiler\Repository\Interfaces\Measure */

        return $repository->findMeasuresForTest($testId);
    }

    /**
     * Gets statistics information for measure.
     *
     * @param int                             $measureId ID of meassure
     * @param \PM\Profiler\Repository\Factory $factory   Repository factory instance
     * @param enum                            $type      One of PM\Profiler\Monitor\Enum\Type
     *
     * @return array
     */
    public function getMeasureStatistics($measureId, Repository\Factory $factory, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type, $measureId); /* @var $repository \PM\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasureStatistics($measureId);
    }

    /**
     * Gets list of calls for measure and their parent ID.
     *
     * @param int                             $measureId ID of measure
     * @param \PM\Profiler\Repository\Factory $factory   Repository factory instance
     * @param int                             $parentId  ID of parent call (default 0)
     * @param enum                            $type      One of PM\Profiler\Monitor\Enum\Type
     *
     * @return array
     */
    public function getMeasureCallStack($measureId, Repository\Factory $factory, $parentId = 0, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type, $measureId); /* @var $repository \PM\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasureCallStack($measureId, $parentId);
    }

    /**
     * Gets list of statistics for each call by given measure ID.
     *
     * @param int                             $measureId ID of measure
     * @param \PM\Profiler\Repository\Factory $factory   Repository factory instance
     * @param enum                            $type      One of PM\Profiler\Monitor\Enum\Type
     *
     * @return array
     */
    public function getMeasureCallsStatistic($measureId, Repository\Factory $factory, $type = Type::SESSION) {
        $repository = $factory->getMeasure($type, $measureId); /* @var $repository \PM\Profiler\Repository\Interfaces\Measure */

        return $repository->getMeasureCallsStatistic($measureId);
    }

    /**
     * Delete measure stored in file.
     *
     * @param int                             $measureId ID of measure
     * @param \PM\Profiler\Repository\Factory $factory   Repository factory instance
     *
     * @return boolean
     */
    public function deleteFileMeasure($measureId, Repository\Factory $factory) {
        $repository = $factory->getMeasure(Type::FILE, $measureId);
        /* @var $repository \PM\Profiler\Repository\Measure\Cache */

        return $repository->deleteMeasure($measureId);
    }
}
