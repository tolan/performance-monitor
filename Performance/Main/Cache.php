<?php

namespace PM\Main;

/**
 * This script defines class for caching.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Cache {

    const DEFAULT_NAMESPACE = 'Performance';

    /**
     * Cache driver.
     *
     * @var Cache\Interfaces\Driver
     */
    private $_driver;

    /**
     * Construct method.
     *
     * @param Cache\Interfaces\Driver $driver    Cache driver
     * @param string                  $namespace Namespace
     * @param Config                  $config    Config instance
     *
     * @return void
     */
    public function __construct($driver = null, $namespace = self::DEFAULT_NAMESPACE, Config $config = null) {
        if ($driver && !($driver instanceof Cache\Interfaces\Driver)) {
            throw new Exception('Cache driver must be instance of Cache\Interfaces\Driver.');
        } elseif (!$driver) {
            $defaultDriver = __NAMESPACE__.'\\Cache\\Session';
            $configCache   = $config->get('cache', array());
            $driverClass   = array_key_exists('driver', $configCache) ? $configCache['driver'] : $defaultDriver;

            if (!is_subclass_of($driverClass, __NAMESPACE__.'\\Cache\Interfaces\Driver')) {
                throw new Exception('Configuration is not valid, it must be instance of Cache\Interfaces\Driver.');
            }

            $driver = new $driverClass($namespace, $config);
        }

        $this->_driver = $driver;
    }

    /**
     * Sets cache driver for storing data.
     *
     * @param Cache\Interfaces\Driver $driver Cache driver instance
     *
     * @return Cache
     */
    public function setDriver(Cache\Interfaces\Driver $driver) {
        $this->_driver = $driver;

        return $this;
    }

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     */
    public function load($name = null) {
        return $this->_driver->load($name);
    }

    /**
     * Sets value to variable by name.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Values
     *
     * @return Cache
     */
    public function save($name, $value) {
        $this->_driver->save($name, $value);

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
        return $this->_driver->has($name);
    }

    /**
     * Clean variable by name.
     *
     * @param string $name Name of variable
     *
     * @return Cache
     */
    public function clean($name = null) {
        $this->_driver->clean($name);

        return $this;
    }

    /**
     * Flush unsaved data to storage.
     *
     * @return Cache
     */
    public function commit() {
        $this->_driver->commit();

        return $this;
    }
}
