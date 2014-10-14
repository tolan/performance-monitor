<?php

namespace PF\Statistic\Gearman;

use PF\Main\Exception;
use PF\Statistic\Engine\State;
use PF\Statistic\Entity;

/**
 * This script defines statistic gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Worker extends \PF\Main\Abstracts\Gearman\Worker {

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

        $generator  = $this->getProvider()->get('PF\Statistic\Engine\Generator'); /* @var $generator \PF\Statistic\Engine\Generator */
        $service    = $this->getProvider()->get('PF\Statistic\Service\Statistic'); /* @var $service \PF\Statistic\Service\Statistic */
        $runService = $this->getProvider()->get('PF\Statistic\Service\Run'); /* @var $runService \PF\Statistic\Service\Run */
        $repository = $this->getProvider()->get('PF\Statistic\Repository\Run'); /* @var $repository \PF\Statistic\Repository\Run */

        $stateObserver = $this->getProvider()->get('PF\Statistic\Engine\Helper\State'); /* @var $stateObserver \PF\Statistic\Engine\Helper\State */
        $stateObserver->setRun($run);

        $state = $this->getProvider()->prototype('PF\Statistic\Engine\State'); /* @var $state \PF\Statistic\Engine\State */
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
        $templateService = $this->getProvider()->get('PF\Statistic\Service\Template'); /* @var $templateService \PF\Statistic\Service\Template */
        $runService      = $this->getProvider()->get('PF\Statistic\Service\Run'); /* @var $runService \PF\Statistic\Service\Run */
        $runRepository   = $this->getProvider()->get('PF\Statistic\Repository\Run'); /* @var $runRepository \PF\Statistic\Repository\Run */;

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
        $setService    = $this->getProvider()->get('PF\Statistic\Service\Set'); /* @var $setService \PF\Statistic\Service\Set */
        $setRepository = $this->getProvider()->get('PF\Statistic\Repository\Set'); /* @var $setRepository \PF\Statistic\Repository\Set */;
        $runService    = $this->getProvider()->get('PF\Statistic\Service\Run'); /* @var $runService \PF\Statistic\Service\Run */

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
        $templateService    = $this->getProvider()->get('PF\Statistic\Service\Template'); /* @var $templateService \PF\Statistic\Service\Template */
        $templateRepository = $this->getProvider()->get('PF\Statistic\Repository\Template'); /* @var $templateRepository \PF\Statistic\Repository\Template */;
        $viewService        = $this->getProvider()->get('PF\Statistic\Service\View'); /* @var $viewService \PF\Statistic\Service\View */
        $searchService      = $this->getProvider()->get('PF\Search\Service\Template'); /* @var $searchService \PF\Search\Service\Template */

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
