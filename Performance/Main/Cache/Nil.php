<?php

namespace PM\Main\Cache;

/**
 * This script defines driver class for cache which save data to NULL. It means that it doesn't save any data and load ruterns null.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Nil implements Interfaces\Driver {

    /**
     * Load variable from cache. It always returns null.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     */
    public function load($name = null) {
        return null;
    }

    /**
     * Sets value to variable by name. It doesn't save anything.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Value for save
     *
     * @return Nil
     */
    public function save($name, $value) {
        return $this;
    }

    /**
     * Returns that variable is set. It is always false.
     *
     * @param string $name Name of variable
     *
     * @return boolean
     */
    public function has($name) {
        return false;
    }

    /**
     * Clean variable by name.
     *
     * @param string $name Name of variable
     *
     * @return Nil
     */
    public function clean($name=null) {
        return $this;
    }

    /**
     * Flush unsaved data to storage.
     *
     * @return Nil
     */
    public function commit() {
        return $this;
    }
}
