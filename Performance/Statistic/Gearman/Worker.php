<?php

namespace PM\Statistic\Gearman;

use PM\Main\Exception;
use PM\Statistic\Engine\State;
use PM\Statistic\Entity;

/**
 * This script defines statistic gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Worker extends \PM\Main\Abstracts\Gearman\Worker {

    /**
     * Process method which generates statistic data and saves it.
     *
     * @return void
     */
    public function process() {
        $data = $this->getMessageData();

        $run = $this->_getRun($data['id']);
        $set = $this->_getSet($run->getStatisticSetId());

        $templates = $this->_getTemplates($set->getTemplates());

        $generator  = $this->getProvider()->get('PM\Statistic\Engine\Generator'); /* @var $generator \PM\Statistic\Engine\Generator */
        $service    = $this->getProvider()->get('PM\Statistic\Service\Statistic'); /* @var $service \PM\Statistic\Service\Statistic */
        $runService = $this->getProvider()->get('PM\Statistic\Service\Run'); /* @var $runService \PM\Statistic\Service\Run */
        $repository = $this->getProvider()->get('PM\Statistic\Repository\Run'); /* @var $repository \PM\Statistic\Repository\Run */

        $stateObserver = $this->getProvider()->get('PM\Statistic\Engine\Helper\State'); /* @var $stateObserver \PM\Statistic\Engine\Helper\State */
        $stateObserver->setRun($run);

        $state = $this->getProvider()->prototype('PM\Statistic\Engine\State'); /* @var $state \PM\Statistic\Engine\State */
        $state->setState(State::STATE_IDLE);
        $state->attach($stateObserver);

        try {
            $runService->updateRun($run->setStarted(time())->toArray(), $repository);
            $state->setState(State::STATE_RUNNING);
            foreach ($templates as $template) {
                $statistics = $generator->generateStatistic($template);

                foreach ($statistics as $key => $statistic) {
                    $statistic->set('statisticSetRunId', $run->getId());
                    $statistic->set('statisticViewLineId', $statistic->getLineId());

                    $service->createStatistic($statistic->toArray(), $repository);
                }
            }

            $state->setState(State::STATE_DONE);
        } catch (Exception $exc) {
            $this->getProvider()->get('log')->error($exc);
            $state->setState(State::STATE_ERROR);
        }
    }

    /**
     * Returns run entity by id.
     *
     * @param int $id Id of statistic run
     *
     * @return Entity\Run
     */
    private function _getRun($id) {
        $templateService = $this->getProvider()->get('PM\Statistic\Service\Template'); /* @var $templateService \PM\Statistic\Service\Template */
        $runService      = $this->getProvider()->get('PM\Statistic\Service\Run'); /* @var $runService \PM\Statistic\Service\Run */
        $runRepository   = $this->getProvider()->get('PM\Statistic\Repository\Run'); /* @var $runRepository \PM\Statistic\Repository\Run */;

        return $runService->getRun($id, $runRepository, $templateService, false, false);
    }

    /**
     * Returns statistic set entity by id.
     *
     * @param int $id Id of statistic set
     *
     * @return Entity\Set
     */
    private function _getSet($id) {
        $setService    = $this->getProvider()->get('PM\Statistic\Service\Set'); /* @var $setService \PM\Statistic\Service\Set */
        $setRepository = $this->getProvider()->get('PM\Statistic\Repository\Set'); /* @var $setRepository \PM\Statistic\Repository\Set */;
        $runService    = $this->getProvider()->get('PM\Statistic\Service\Run'); /* @var $runService \PM\Statistic\Service\Run */

        return $setService->getSet($id, $setRepository, $runService, false);
    }

    /**
     * Returns list of statistic templates which are attached to statistic set.
     *
     * @param array $ids Set of template ids
     *
     * @return array
     */
    private function _getTemplates(array $ids) {
        $templateService    = $this->getProvider()->get('PM\Statistic\Service\Template'); /* @var $templateService \PM\Statistic\Service\Template */
        $templateRepository = $this->getProvider()->get('PM\Statistic\Repository\Template'); /* @var $templateRepository \PM\Statistic\Repository\Template */;
        $viewService        = $this->getProvider()->get('PM\Statistic\Service\View'); /* @var $viewService \PM\Statistic\Service\View */
        $searchService      = $this->getProvider()->get('PM\Search\Service\Template'); /* @var $searchService \PM\Search\Service\Template */

        $templates = array();

        foreach ($ids as $id) {
            $templates[] = $templateService->getTemplate($id, $templateRepository, $viewService, $searchService);
        }

        return $templates;
    }

    /**
     * Result for asynchronous job is true.
     *
     * @return boolean
     */
    public function getResult() {
        return true;
    }
}
