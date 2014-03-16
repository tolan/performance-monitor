<?php

namespace PF\Main\Commander\Interfaces;

use PF\Main\Commander\Result;
use PF\Main\Provider;

/**
 * This script defines interface for execution of executor (set of commands).
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Execution {

    /**
     * Construct method.
     *
     * @param string|\Closure $command Name of function in scope or Closure function
     * @param Object          $scope   If command is only name of function then must be defined scope (class) where is command triggered
     */
    public function __construct($command, $scope = null);

    /**
     * This method triggers the input command (in scope).
     *
     * @param \PF\Main\Commander\Result $result   Entity for saving data from command
     * @param \PF\Main\Provider         $provider Provider instance
     */
    public function execute(Result $result, Provider $provider);
}
