<?php

/**
 * This script defines class for caching.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Cache {
    const SESSION_NAME      = 'Cache';
    const DEFAULT_NAMESPACE = 'Performance';

    /**
     * Namespace of cache.
     *
     * @var string
     */
    private $_namespace = self::DEFAULT_NAMESPACE;

    /**
     * Array with values.
     *
     * @var array
     */
    private $_cache = array();

    /**
     * Construct method.
     *
     * @param string $namespace Namespace
     */
    public function __construct($namespace = self::DEFAULT_NAMESPACE) {
        $this->_namespace = $namespace;

        if (isset($_SESSION[$namespace])) {
            $this->_cache = unserialize($_SESSION[self::SESSION_NAME][$namespace]);
        }
    }

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     *
     * @throws Performance_Main_Exception Throws when variable is not defined
     */
    public function load($name) {
        if (!$this->has($name)) {
            throw new Performance_Main_Exception('Undefined cache variable.');
        }

        return $this->_cache[$name];
    }

    /**
     * Sets value to variable by name.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Values
     *
     * @return Performance_Main_Cache
     */
    public function save($name, $value) {
        $this->_cache[$name] = $value;

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
        return isset($this->_cache[$name]);
    }

    /**
     * Clean variable by name.
     *
     * @param string $name Name of variable
     *
     * @return Performance_Main_Cache
     *
     * @throws Performance_Main_Exception Throws when variable is not set.
     */
    public function clean($name) {
        if (!$this->has($name)) {
            throw new Performance_Main_Exception('Undefined cache variable.');
        }

        unset($this->_cache[$name]);

        return $this;
    }

    /**
     * Destruct function saves cache variables into session.
     *
     * @return void
     */
    public function __destruct() {
        if (!empty($this->_cache)) {
            $_SESSION[self::SESSION_NAME][$this->_namespace] = serialize($this->_cache);
        } elseif (isset($_SESSION[self::SESSION_NAME][$this->_namespace])) {
            unset($_SESSION[self::SESSION_NAME][$this->_namespace]);
        }
    }
}
