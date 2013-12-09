<?php

/**
 * This script defines class for application configuration.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Config {

    /**
     * Configuration data
     *
     * @var array
     */
    private $_data = array();

    /**
     * Magic method for basic function (set, get).
     *
     * @param string $name      Method name
     * @param array  $arguments Input data
     *
     * @throws Performance_Main_Exception
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        $method = substr($name, 0, 3);
        $property = substr($name, 3);

        switch ($method) {
            case 'set':
                return $this->set($property, $arguments[0]);
            case 'get':
                return $this->get($property);
            default:
                throw new Performance_Main_Exception('Undefined function');
        }
    }

    /**
     * This method load configuration data from array
     *
     * @param array $array Configuration data
     *
     * @return Performance_Main_Config
     */
    public function fromArray(array $array = null) {
        foreach ($array as $key => $item) {
            $this->set($key, $item);
        }

        return $this;
    }

    /**
     * This return all configuration data.
     *
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }

    /**
     * Sets configuration option
     *
     * @param string $name Name of option
     * @param mixed  $data Configuration data
     *
     * @return Performance_Main_Config
     */
    public function set($name, $data) {
        $this->_data[lcfirst($name)] = $data;

        return $this;
    }

    /**
     * Returns configuration option by name.
     *
     * @param string $name Name of configuration option
     *
     * @return mixed Configuration data
     *
     * @throws Performance_Main_Exception Throws when option is not defined.
     */
    public function get($name) {
        if ($this->hasOwnProperty($name) === false) {
            throw new Performance_Main_Exception('Property "'.lcfirst($name).'" is not defined.');
        }

        return $this->_data[lcfirst($name)];
    }

    /**
     * This method unset configuration data. When name is null then it clear all configuration.
     *
     * @param string $name Name of configuration option
     *
     * @return Performance_Main_Config
     *
     * @throws Performance_Main_Exception Throws when option is not defined.
     */
    public function reset($name=null) {
        if($name === null) {
            $this->_data = array();
        } elseif($this->hasOwnProperty($name)) {
            unset($this->_data[lcfirst($name)]);
        } else {
            throw new Performance_Main_Exception('Property "'.lcfirst($name).'" is not defined.');
        }

        return $this;
    }

    /**
     * This checks that option is defined.
     *
     * @param string $name Name of configuration option
     *
     * @return boolean TRUE when option is defined
     */
    public function hasOwnProperty($name) {
        return key_exists(lcfirst($name), $this->_data);
    }
}
