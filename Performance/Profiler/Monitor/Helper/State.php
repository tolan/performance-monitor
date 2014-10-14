<?php

namespace PF\Profiler\Monitor\Helper;

use PF\Main\Interfaces\Observer;
use PF\Main\Interfaces\Observable;
use PF\Main\Commander;
use PF\Main\Commander\Executor;
use PF\Profiler\Monitor\Repository;
use PF\Profiler\Service;
use PF\Profiler\Entity;
use PF\Profiler\Monitor\Enum\Type;

/**
 * This script defines helper class for updating state of test.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class State implements Observer {

    /**
     * Commander instance
     *
     * @var Commander
     */
    private $_commander = null;

    /**
     * Measure service instance.
     *
     * @var Service\Measure
     */
    private $_measureService = null;

    /**
     * Test service instatnce.
     *
     * @var Service\Test
     */
    private $_testService = null;

    /**
     * Construct method.
     *
     * @param Commander       $commander      Commander instance
     * @param Service\Measure $measureService Measure service instance
     * @param Service\Test    $testService    Test service instance
     *
     * @return void
     */
    public function __construct(Commander $commander, Service\Measure $measureService, Service\Test $testService) {
        $this->_commander       = $commander;
        $this->_measureService = $measureService;
        $this->_testService     = $testService;
    }

    /**
     * It updates test state by observable subject measure id.
     *
     * @param Observable $subject Observble subject (Repository\AbstractRepository)
     * 
     * @return boolean
     */
    public function updateObserver (Observable $subject) {
        /* @var $subject Repository\AbstractRepository */
        $measureId = $subject->getMeasureId();
        $executor  = $this->_commander->getExecutor(__CLASS__)->clean(); /* @var $executor Executor */

        $executor->add('getMeasure', $this->_measureService)
            ->getResult()
            ->setMeasureId($measureId)
            ->setType(Type::MYSQL);

        $measure = $executor->execute()->getData(); /* @var $measure Entity\Measure */
        $testId  = $measure->getTestId();

        $executor->clean()
            ->add('updateTestState', $this->_testService)
            ->getResult()
            ->setTestId($testId);
        $executor->execute();

        return true;
    }

}
