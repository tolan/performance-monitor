<?php

namespace PM\Main\Commander\Interfaces;

/**
 * This script defines interface for commander.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Commander {

    /**
     * Returns executor (set of commands) by defined name. If executor doesn't exist then it create new instance.
     *
     * @param string $name Name of command set
     *
     * @return \PM\Main\Commander\Executor
     */
    public function getExecutor($name);

    /**
     * This method remove executor with given name.
     *
     * @param string $name Name of command set
     */
    public function destroyExecutor($name);

    /**
     * Returns a flag if there is an executor.
     *
     * @param string $name Name of command set
     *
     * @return boolean
     */
    public function hasExecutor($name);
}
