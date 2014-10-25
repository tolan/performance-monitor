<?php

namespace PM\Main\Commander;

use PM\Main\Provider;

/**
 * This script defines class for executor (set of commands).
 * Executor save commands and provide trigger execute on all commands with sharing one Result instance. The result instance can be shared between executors.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Executor implements Interfaces\Executor {

    /**
     * Result instance.
     *
     * @var \PM\Main\Commander\Result
     */
    private $_result;

    /**
     * Provider instance.
     *
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * Set of executions (commands)
     *
     * @var \PM\Main\Commander\Execution[]
     */
    private $_executions = array();

    /**
     * Construct method.
     *
     * @param \PM\Main\Commander\Result $result   Entity for saving data from commands
     * @param \PM\Main\Provider         $provider Provider instance
     *
     * @return void
     */
    public function __construct(Result $result, Provider $provider) {
        $this->_result   = $result;
        $this->_provider = $provider;
    }

    /**
     * Adds command to set of commands.
     *
     * @param string|\Closure $command Name of function in scope or Closure function
     * @param Object          $scope   If command is only name of function then must be defined scope (class) where is command triggered
     * @param mixed           $data    Input data for execute
     *
     * @return \PM\Main\Commander\Executor
     */
    public function add($command, $scope = null, $data = array()) {
        $this->_executions[] = new Execution($command, $scope);

        $this->getResult()->fromArray($data);

        return $this;
    }

    /**
     * Returns all executions (set of commands).
     *
     * @return \PM\Main\Commander\Execution[]
     */
    public function get() {
        return $this->_executions;
    }

    /**
     * Clean all executions (set of commands) from list.
     *
     * @return \PM\Main\Commander\Executor
     */
    public function clean() {
        $this->_executions = array();

        return $this;
    }

    /**
     * Returns result instance with processed data.
     *
     * @return \PM\Main\Commander\Result
     */
    public function getResult() {
        return $this->_result;
    }

    /**
     * It provides set result instance for sharing one instance between executors.
     *
     * @param \PM\Main\Commander\Result $result Entity for saving data from commands
     *
     * @return \PM\Main\Commander\Executor
     */
    public function setResult(Result $result) {
        $this->_result = $result;

        return $this;
    }

    /**
     * This method triggers execute on all executions (all commands).
     *
     * @return \PM\Main\Commander\Result
     */
    public function execute() {
        $result = $this->_result;
        foreach ($this->_executions as $command) {
            $command->execute($result, $this->_provider);
        }

        return $result;
    }
}
