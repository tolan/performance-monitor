<?php

namespace PM\Main\Cache\Interfaces;

/**
 * Interface for cache driver.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Driver {

    const SESSION_NAME      = 'Cache';
    const DEFAULT_NAMESPACE = 'Performance';

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     *
     * @throws \PM\Main\Cache\Exception Throws when variable is not defined
     */
    public function load($name = null);

    /**
     * Sets value to variable by name.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Value for save
     *
     * @return \PM\Main\Cache\Interfaces\Driver
     */
    public function save($name, $value);

    /**
     * Returns that variable is set.
     *
     * @param string $name Name of variable
     *
     * @return boolean
     */
    public function has($name);

    /**
     * Clean variable by name.
     *
     * @param string $name Name of variable
     *
     * @return \PM\Main\Cache\Interfaces\Driver
     *
     * @throws \PM\Main\Cache\Exception Throws when variable is not set.
     */
    public function clean($name=null);

    /**
     * Flush unsaved data to storage.
     *
     * @return \PM\Main\Cache\Interfaces\Driver
     */
    public function commit();
}
