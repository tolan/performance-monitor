<?php

namespace PM\Main\Cache;

/**
 * Abstract class for cache driver
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractDriver implements Interfaces\Driver {

    /**
     * Storage for cached data.
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     *
     * @throws \PM\Main\Cache\Exception Throws when variable is not defined
     */
    public function load($name = null) {
        $data = array();
        if ($name === null) {
            $data = $this->_data;
        } elseif ($this->has($name)) {
            $data = $this->_data[$name];
        } else {
            throw new Exception('Undefined cache variable.');
        }

        return $data;
    }

    /**
     * Sets value to variable by name.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Value for save
     *
     * @return \PM\Main\Cache\AbstractDriver
     */
    public function save($name, $value) {
        $this->_data[$name] = $value;

        return $this;
    }

    /**
     * Returns that variable is set.
     *
     * @param string $name Name of variable
     *
     * @return boolean
     */
    public function has($name) {
        return array_key_exists($name, $this->_data);
    }

    /**
     * Clean variable by name.
     *
     * @param string $name Name of variable
     *
     * @return \PM\Main\Cache\AbstractDriver
     *
     * @throws \PM\Main\Cache\Exception Throws when variable is not set.
     */
    public function clean($name=null) {
        if ($name === null) {
            $this->_data = array();
        } else if (!$this->has($name)) {
            throw new Exception('Undefined cache variable.');
        } else {
            unset($this->_data[$name]);
        }

        return $this;
    }

    /**
     * Flush unsaved data to storage.
     *
     * @return \PM\Main\Cache\AbstractDriver
     */
    public function commit() {
        return $this;
    }
}
