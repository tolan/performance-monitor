<?php

namespace PM\Statistic\Engine\Helper;

use PM\Main\Commander;
use PM\Main\Interfaces;
use PM\Statistic\Engine;
use PM\Statistic\Entity;
use PM\Statistic\Service;

/**
 * This script defines helper class for updating state of statistic run.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class State implements Interfaces\Observer {

    /**
     * Commander executor instance.
     *
     * @var Commander\Executor
     */
    private $_executor;

    /**
     * Statistic run service instance.
     *
     * @var Service\Run
     */
    private $_service;

    /**
     * Construct method.
     *
     * @param Commander   $commander Commander instance
     * @param Service\Run $service   Statistic run service instance
     *
     * @return void
     */
    public function __construct(Commander $commander, Service\Run $service) {
        $this->_executor = $commander->getExecutor(__CLASS__)->clean();
        $this->_service  = $service;
    }

    /**
     * Sets run entity for updating state.
     *
     * @param Entity\Run $run Entity run instance
     *
     * @return State
     */
    public function setRun(Entity\Run $run) {
        $this->_executor->getResult()->setRun($run);

        return $this;
    }

    /**
     * It updates run state by observable subject state.
     *
     * @param Observable $subject Observble subject (Engine\State)
     *
     * @return boolean
     */
    public function updateObserver(Interfaces\Observable $subject) {
        /* @var $subject Engine\State */

        $state = $subject->getState();

        $this->_executor->clean()
            ->add('updateState', $this->_service)
            ->getResult()
            ->setState($state);

        $this->_executor->execute();

        return true;
    }
}
