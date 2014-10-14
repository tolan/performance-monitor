<?php

namespace PF\Statistic\Service;

use PF\Statistic\Repository;
use PF\Statistic\Entity;
use PF\Main\Abstracts\Service;

/**
 * This script defines class for run service of statistic data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Run extends Service {

    /**
     * Return all runs for statistic set.
     *
     * @param int            $setId      Id of statistic set
     * @param Repository\Run $repository Repository run instance
     *
     * @return Entity\Run[]
     */
    public function getRunsForSet($setId, Repository\Run $repository) {
        return $repository->getRunsForSet($setId);
    }

    /**
     * Deletes statistic run entity by given id.
     *
     * @param int            $id         Id of statistic run
     * @param Repository\Run $repository Repository run instance
     *
     * @return int
     */
    public function deleteRun($id, Repository\Run $repository) {
        return $repository->deleteRun($id);
    }

    /**
     * Creates new statistic run entity.
     *
     * @param int            $setId      Id of statistic set
     * @param Repository\Run $repository Repository run instance
     *
     * @return Entity\Run
     */
    public function createRun($setId, Repository\Run $repository) {
        $run = new Entity\Run(
            array('statisticSetId' => $setId)
        );

        $repository->createRun($run);

        return $run;
    }

    /**
     * Returns statistic run entity by given id.
     *
     * @param int            $id              Id of statistic run
     * @param Repository\Run $repository      Repository run instance
     * @param Template       $templateService Template service instance
     * @param booelan        $includeData     Flag for add statistic data
     * @param boolean        $includeTemplate Flag for add attached statistic templates
     *
     * @return Entity\Run
     */

    public function getRun($id, Repository\Run $repository, Template $templateService, $includeData = false, $includeTemplate = false) {
        $run = $repository->getRun($id);

        if ($includeData === true) {
            $run->setData($repository->getDataForRun($id));
        }

        if ($includeTemplate === true) {
            $templateIds = $repository->getTemplateIdsForRun($id);
            $templates = array();

            foreach ($templateIds as $templateId) {
                $executor = $this->getExecutor()
                    ->clean()
                    ->add('getTemplate', $templateService);
                $executor->getResult()
                    ->setId($templateId);

                $templates[] = $executor->execute()->getData();
            }

            $run->setTemplates($templates);
        }

        return $run;
    }

    /**
     * Updates statistic run entity by given data.
     *
     * @param array          $data       Statistic run data
     * @param Repository\Run $repository Repository run instance
     *
     * @return int
     */
    public function updateRun($data, Repository\Run $repository) {
        $run = new Entity\Run($data);

        $repository->updateRun($run);

        return $run;
    }

    /**
     * It updates state of statistic run.
     *
     * @param string         $state      One of enum \PF\Statistic\Enum\Run\State
     * @param Entity\Run     $run        Statistic run entity instance
     * @param Repository\Run $repository Repository run instance
     *
     * @return Entity\Run
     */
    public function updateState($state, Entity\Run $run, Repository\Run $repository) {
        $run->setState($state);

        $repository->updateRun($run);

        return $run;
    }
}
