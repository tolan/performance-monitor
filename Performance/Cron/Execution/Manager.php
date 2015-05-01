<?php

namespace PM\Cron\Execution;

use PM\Main\Provider;
use PM\Main\Commander\Executor;
use PM\Cron;

/**
 * This script defines class for managing cron execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Manager {

    /**
     * Singleton instance.
     *
     * @var Manager
     */
    private static $_selfInstance = null;

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Construct method for set dependencies.
     *
     * @param Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Return singleton instance.
     *
     * @return Manager
     */
    public static function getInstance(Provider $provider) {
        if (self::$_selfInstance === null) {
            self::$_selfInstance = new self($provider);
        }

        return self::$_selfInstance;
    }

    /**
     * It process task in time. It starts or check current state of task.
     *
     * @param Cron\Entity\Task        $task       Cron entity task instance
     * @param Cron\Parser             $parser     Cron parser instance
     * @param Cron\Service\TriggerLog $logService Cron trigger log service instance
     * @param Cron\Parser\Date        $datetime   Cron parser date with time execution
     *
     * @return Manager
     */
    public function processTask(Cron\Entity\Task $task, Cron\Parser $parser, Cron\Service\TriggerLog $logService, Cron\Parser\Date $datetime = null) {
        if ($datetime === null) {
            $datetime = new Cron\Parser\Date();
        }

        foreach ($task->getTriggers() as $trigger) {
            $this->_prepareParser($trigger->getTimer(), $datetime, $parser);
            $triggerLog = $this->_manageTriggerLog($trigger, $parser, $logService);

            $result = null;

            if ($triggerLog->getState() === Cron\Enum\TriggerState::IDLE) {
                $result = $this->_start($trigger, $datetime);
            } elseif ($triggerLog->getState() === Cron\Enum\TriggerState::RUNNING) {
                $result = $this->_check($trigger, $datetime);
            }

            if ($result !== null) {
                $this->_saveResult($result, $triggerLog, $logService);
            }
        }

        return $this;
    }

    /**
     * It prepare cron parser with execution date and timer expression.
     *
     * @param Cron\Entity\Timer $timer Timer expression instance from cron entity task
     * @param Cron\Parser\Date $datetime Execution date instance
     * @param Cron\Parser $parser Cron parser instance
     *
     * @return Cron\Parser
     */
    private function _prepareParser(Cron\Entity\Timer $timer, Cron\Parser\Date $datetime, Cron\Parser $parser) {
        $parser->getExpression()
            ->setMinute($timer->getMinute())
            ->setHour($timer->getHour())
            ->setDay($timer->getDay())
            ->setMonth($timer->getMonth())
            ->setDayOfWeek($timer->getDayOfWeek());

        $parser->setDatetime($datetime);

        return $parser;
    }

    /**
     * It manages trigger log. When it doesn't exist then it create new log and set idle state otherwise it sets state by stated date and execution date.
     *
     * @param Cron\Entity\Trigger     $trigger    Cron trigger entity instance from cron task entity
     * @param Cron\Parser             $parser     Cron parser instance
     * @param Cron\Service\TriggerLog $logService Cron trigger log service instance
     *
     * @return Cron\Entity\TriggerLog
     */
    private function _manageTriggerLog(Cron\Entity\Trigger $trigger, Cron\Parser $parser, Cron\Service\TriggerLog $logService) {
        $log = $this->_getExecutor()
            ->add('getLastLogForTrigger', $logService, array('triggerId' => $trigger->getId()))
            ->execute()
            ->getData();
        /* @var $log Cron\Entity\TriggerLog */

        $before = $parser->resolveBefore();
        $actual = $parser->resolveIsActual();
        $next   = $parser->resolveNext();

        $logData = array(
            'cronTriggerId' => $trigger->getId(),
            'state'         => Cron\Enum\TriggerState::IDLE
        );

        if ($log) {
            if ($before->getTimestamp() > $log->getStarted()) {
                $logData['state']   = Cron\Enum\TriggerState::ERROR;
                $logData['started'] = $before->getTimestamp();
                $logData['message'] = 'something is wrong, trigger was not executed';   // TODO translate

                $log = $this->_getExecutor()->add('createLog', $logService, array('data' => $logData))->execute()->getData();
            }

            if ($actual && $actual->getTimestamp() > $log->getStarted()) {
                $logData['state']   = Cron\Enum\TriggerState::IDLE;
                $logData['started'] = $actual->getTimestamp();
                $logData['message'] = '';   // TODO translate

                $log = $this->_getExecutor()->add('createLog', $logService, array('data' => $logData))->execute()->getData();
            }

            if ($next->getTimestamp() < $log->getStarted()) {
                $logData['state']   = Cron\Enum\TriggerState::ERROR;
                $logData['started'] = $before->getTimestamp();
                $logData['message'] = 'something is wrong, trigger is xecuted in future';   // TODO translate

                $log = $this->_getExecutor()->add('createLog', $logService, array('data' => $logData))->execute()->getData();
            }
        } else {
            $logData['started'] = $actual->getTimestamp();

            $log = $this->_getExecutor()->add('createLog', $logService, array('data' => $logData))->execute()->getData();
        }

        return $log;
    }

    /**
     * It starts trigger (it executes all actions and return result of them).
     *
     * @param Cron\Entity\Trigger $trigger  Cron trigger entity instance
     * @param Cron\Parser\Date    $datetime Cron parser date instance with execution datetime
     *
     * @return string
     */
    private function _start(Cron\Entity\Trigger $trigger, Cron\Parser\Date $datetime) {
        $result = array();
        foreach ($trigger->getActions() as $action) {
            $action = new Action($action, $this->_provider, $datetime);
            $result[$action->execute()->getResult()] = null;
        }

        return $this->_resolveResult($result);
    }

    /**
     * It checks trigger.
     *
     * @param Cron\Entity\Trigger $trigger  Cron trigger entity instance
     * @param Cron\Parser\Date    $datetime Cron parser date instance with execution datetime
     *
     * @return string
     */
    private function _check(Cron\Entity\Trigger $trigger, Cron\Parser\Date $datetime) {
        // TODO
        return Cron\Enum\ExecutionResult::ERROR;;
    }

    /**
     * It resolves finally result from list of results.
     *
     * @param array $result List of result from execution
     *
     * @return string
     */
    private function _resolveResult(array $result=array()) {
        $finally = Cron\Enum\ExecutionResult::SUCCESS;
        if (array_key_exists(Cron\Enum\ExecutionResult::FATAL, $result)) {
            $finally = Cron\Enum\ExecutionResult::FATAL;
        } elseif (array_key_exists(Cron\Enum\ExecutionResult::ERROR, $result)){
            if (array_key_exists(Cron\Enum\ExecutionResult::SUCCESS, $result)) {
                $finally = Cron\Enum\ExecutionResult::PARTIAL;
            } else {
                $finally = Cron\Enum\ExecutionResult::ERROR;
            }
        }

        return $finally;
    }

    /**
     * It saves result from trigger exution to trigger log instance.
     *
     * @param string                  $result     Value from enum Cron\Enum\ExecutionResult
     * @param Cron\Entity\TriggerLog  $triggerLog Cron trigger log entity instance
     * @param Cron\Service\TriggerLog $logService Cron trigger log service instance
     *
     * @return Cron\Execution\Manager
     */
    private function _saveResult($result, Cron\Entity\TriggerLog $triggerLog, Cron\Service\TriggerLog $logService) {
        $triggerLog->setState($result);
        $this->_getExecutor()
            ->add(
                'updateLog',
                $logService,
                array('data' => $triggerLog->toArray())
            )
            ->execute();

        return $this;
    }

    /**
     * Returns executor instance.
     *
     * @return Executor
     */
    private function _getExecutor() {
        return $this->_provider->get('commander')
            ->getExecutor(__CLASS__)
            ->clean();
    }
}
