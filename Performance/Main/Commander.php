<?php

namespace PF\Main;

/**
 * This script defines class for create and store set of commands under defined name.
 * The named set of commands is available over whole framework.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Commander implements Commander\Interfaces\Commander {

    /**
     * Provider instance.
     *
     * @var \PF\Main\Provider
     */
    private $_provider;

    /**
     * List of executors with name.
     *
     * @var \PF\Main\Commander\Executor[]
     */
    private $_executors = array();

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Returns executor (set of commands) by defined name. If executor doesn't exist then it create new instance.
     *
     * @param string $name Name of command set
     *
     * @return \PF\Main\Commander\Executor
     */
    public function getExecutor($name) {
        $this->_validateName($name);

        if (!$this->hasExecutor($name)) {
            $this->_createExecutor($name);
        }

        return $this->_executors[$name];
    }

    /**
     * This method remove executor with given name. Attention the executor must exist.
     *
     * @param string $name Name of command set
     *
     * @return \PF\Main\Commander
     *
     * @throws \PF\Main\Exception Throws when executor doesn't exist.
     */
    public function destroyExecutor($name) {
        $this->_validateName($name);

        if ($this->hasExecutor($name)) {
            unset($this->_executors[$name]);
        } else {
            throw new Exception('Executor with name "'.$name.'" doesn\'t exist.');
        }

        return $this;
    }

    /**
     * Returns a flag if there is an executor.
     *
     * @param string $name Name of command set
     *
     * @return boolean
     */
    public function hasExecutor($name) {
        return array_key_exists($name, $this->_executors);
    }

    /**
     * Validate executor name. It must be string.
     *
     * @param string $name Name of command set
     *
     * @throws \PF\Main\Exception Throws when executor name is not string.
     *
     * @return \PF\Main\Commander
     */
    private function _validateName($name) {
        if (!is_string($name)) {
            throw new Exception('Executor name is not valid.');
        }

        return $this;
    }

    /**
     * Create new executor by given name and save it into list of executors.
     *
     * @param string $name Name of command set
     * 
     * @return \PF\Main\Commander
     */
    private function _createExecutor($name) {
        $this->_executors[$name] = $this->_provider->prototype('PF\Main\Commander\Executor', true);

        return $this;
    }
}
