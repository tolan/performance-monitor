<?php

namespace PM\Cron\Execution\Task;

use PM\Cron\Execution\Context\AbstractEntity;
use PM\Cron\Enum\ExecutionResult;
use PM\Cron\Execution\Exception;

use PM\Main\Commander\Executor;
use PM\Main\Commander;
use PM\Main\Utils;

/**
 * This script defines abstract class for execute task.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
abstract class AbstractTask {

    /**
     * Result of execution.
     *
     * @var string
     */
    private $_result = null;

    /**
     * Commander instance.
     *
     * @var Commander
     */
    private $_commander = null;

    /**
     * Utils instance.
     *
     * @var Utils
     */
    private $_utils = null;

    /**
     * Construct method for initiate dependencies.
     *
     * @param Commander $commander Commander instance
     * @param Utils     $utils     Utils instance
     *
     * @return void
     */
    public function __construct(Commander $commander, Utils $utils) {
        $this->_commander = $commander;
        $this->_utils     = $utils;
    }

    /**
     * It executes task with entity.
     *
     * @param AbstractEntity $entity Context entity for executing with it
     *
     * @return AbstractTask
     *
     * @throws Exception It throws when run method doesn't exist. It sets result to ERROR.
     */
    public function run(AbstractEntity $entity) {
        $this->_result = ExecutionResult::SUCCESS;
        try {
            $methodName = $this->_utils->toCamelCase('run_'.$this->_utils->getShortName($entity));

            if (method_exists($this, $methodName)) {
                $this->_commander
                    ->getExecutor(uniqid(__CLASS__))
                    ->clean()
                    ->add($methodName, $this, array($this->_getEntityAttributeName($methodName, $entity) => $entity))
                    ->execute();
            } else {
                throw new Exception('Method '.$methodName.' doesn\'t exist.');
            }
        } catch (Exception $ex) {
            $this->_result = ExecutionResult::ERROR;
        } catch (\Exception $ex) {
            $this->_result = ExecutionResult::FATAL;
        }

        return $this;
    }

    /**
     * Gets result of execution.
     *
     * @return string
     */
    public function getResult() {
        return $this->_result;
    }

    /**
     * Return instance of executor.
     *
     * @return Executor
     */
    protected function getExecutor() {
        return $this->_commander
            ->getExecutor(get_called_class())
            ->clean();
    }

    /**
     * It executes the executor. It allows wrap the executor.
     *
     * @param Executor $executor Executor instance
     *
     * @return AbstractTask
     */
    protected function execute(Executor $executor) {
        $executor->execute();

        return $this;
    }

    /**
     * It returns resolved name of first attribute which require context entity.
     *
     * @param string         $methodName Name of run method
     * @param AbstractEntity $entity     Context entity
     *
     * @return string
     */
    private function _getEntityAttributeName($methodName, AbstractEntity $entity) {
        $name = 'entity';

        $reflection = new \ReflectionClass($this);
        $reflMethod = $reflection->getMethod($methodName);
        $params     = $reflMethod->getParameters();

        foreach ($params as $param) {
            /* @var $param \ReflectionParameter */
            $class = $param->getClass();
            if ($class) {
                $className = $class->getName();

                if (is_subclass_of($className, get_class($entity))) {
                    $name = $param->getName();
                    break;
                }
            }
        }

        return $name;
    }
}
