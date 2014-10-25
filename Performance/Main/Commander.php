<?php

namespace PM\Main;

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
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * List of executors with name.
     *
     * @var \PM\Main\Commander\Executor[]
     */
    private $_executors = array();

    /**
     * Construct method.
     *
     * @param \PM\Main\Provider $provider Provider instance
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
     * @return \PM\Main\Commander\Executor
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
     * @return \PM\Main\Commander
     *
     * @throws \PM\Main\Exception Throws when executor doesn't exist.
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
     * This method cleans executor by given name. When name is null then it cleans all executors.
     *
     * @param string $name Name of command set
     *
     * @return \PM\Main\Commander
     *
     * @throws \PM\Main\Exception Throws when executor doesn't exist.
     */
    public function cleanExecutor($name = null) {
        if ($name === null) {
            foreach (array_keys($this->_executors) as $name) { /* @var $executor \PM\Main\Commander\Executor */
                $this->cleanExecutor($name);
            }
        } elseif(!$this->hasExecutor($name)) {
            throw new Exception('Executor with name "'.$name.'" doesn\'t exist.');
        } else {
            $this->_executors[$name]->clean();
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
        $this->_validateName($name);

        return array_key_exists($name, $this->_executors);
    }

    /**
     * Validate executor name. It must be string.
     *
     * @param string $name Name of command set
     *
     * @throws \PM\Main\Exception Throws when executor name is not string.
     *
     * @return \PM\Main\Commander
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
     * @return \PM\Main\Commander
     */
    private function _createExecutor($name) {
        $this->_executors[$name] = $this->_provider->prototype('PM\Main\Commander\Executor', true);

        return $this;
    }
}
