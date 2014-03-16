<?php

namespace PF\Main\Commander\Interfaces;

use PF\Main\Commander\Result;
use PF\Main\Provider;

/**
 * This script defines interface for executor (set of commands). It stores set of commands (executions) and provide their triggering.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Executor {

    /**
     * Construct method.
     *
     * @param \PF\Main\Commander\Result $result   Entity for saving data from commands
     * @param \PF\Main\Provider         $provider Provider instance
     */
    public function __construct(Result $result, Provider $provider);

    /**
     * Adds command to set of commands.
     *
     * @param string|\Closure $command Name of function in scope or Closure function
     * @param Object          $scope   If command is only name of function then must be defined scope (class) where is command triggered
     */
    public function add($command, $scope = null);

    /**
     * Returns all executions (set of commands).
     *
     * @return \PF\Main\Commander\Execution[]
     */
    public function get();

    /**
     * Clean all executions (set of commands) from list.
     */
    public function clean();

    /**
     * Returns result instance with processed data.
     *
     * @return \PF\Main\Commander\Result
     */
    public function getResult();

    /**
     * It provides set result instance for sharing one instance between executors.
     *
     * @param \PF\Main\Commander\Result $result Entity for saving data from commands
     */
    public function setResult(Result $result);

    /**
     * This method triggers execute on all executions (all commands).
     */
    public function execute();
}
