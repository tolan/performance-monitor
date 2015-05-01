<?php

namespace PM\Cron\Execution;

use PM\Main\Provider;
use PM\Cron\Entity;
use PM\Cron\Enum;
use PM\Cron\Parser\Date;

/**
 * This script defines class for execute action.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Action {

    /**
     * Action instance.
     *
     * @var Entity\Action
     */
    private $_action = null;

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider = null;

    /**
     * Date instance.
     *
     * @var Date
     */
    private $_date = null;

    /**
     * Result of execution.
     *
     * @var string
     */
    private $_result = null;

    /**
     * Construct method for set dependencies.
     *
     * @param Entity\Action $action   Action entity instance
     * @param Provider      $provider Provider instance
     * @param Date          $date     Date of execution instance
     */
    public function __construct(Entity\Action $action, Provider $provider, Date $date) {
        $this->_action   = $action;
        $this->_provider = $provider;
        $this->_date     = $date;
    }

    /**
     * Returns result of execution.
     *
     * @return string
     */
    public function getResult() {
        return $this->_result;
    }

    /**
     * It executes all tasks in action and process result.
     *
     * @return Action
     */
    public function execute() {
        $this->_result = Enum\ExecutionResult::SUCCESS;
        $context = $this->_getContext();

        $result = array();
        foreach ($context as $entity) {
            foreach ($this->_action->getTasks() as $task) {
                $result[$this->_executeTask($entity, $task)] = null;
            }
        }

        if (array_key_exists(Enum\ExecutionResult::FATAL, $result)) {
            $this->_result = Enum\ExecutionResult::FATAL;
        } elseif (array_key_exists(Enum\ExecutionResult::ERROR, $result)){
            if (array_key_exists(Enum\ExecutionResult::SUCCESS, $result)) {
                $this->_result = Enum\ExecutionResult::PARTIAL;
            } else {
                $this->_result = Enum\ExecutionResult::ERROR;
            }
        }

        return $this;
    }

    /**
     * It executes concrete task of action with context entity.
     *
     * @param Context\AbstractEntity $entity Context entity instance
     * @param Entity\ActionTask      $task   Action task entity instance
     *
     * @return string
     */
    private function _executeTask(Context\AbstractEntity $entity, Entity\ActionTask $task) {
        $className   = __NAMESPACE__.'\\Task\\'.ucfirst($task->getAction());
        $executeTask = $this->_provider->prototype($className); /* @var $executeTask Task\AbstractTask */

        $executeTask->run($entity);

        return $executeTask->getResult();
    }

    /**
     * It creates and resturns context container with context entities by source template.
     *
     * @return Context\Container
     */
    private function _getContext() {
        $source = $this->_action->getSource();
        $search = new Search($this->_provider->get('PM\Search\Engine'));
        $search->setItems($source['items'])
            ->setTemplate($source['template'])
            ->setType($source['type']);

        $context   = new Context\Container();
        $target    = $source['target'];
        $className = __NAMESPACE__.'\\Context\\Entity\\'.ucfirst($target);

        foreach ($search->getResult() as $id) {
            $entity = new $className(); /* @var $entity Context\AbstractEntity */
            $entity->setId($id);
            $entity->setDate($this->_date);

            $context[] = $entity;
        }

        return $context;
    }
}
