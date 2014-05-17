<?php

namespace PF\Main\Cache;

/**
 * This script defines driver class for cache which save data to SESSION.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Session extends AbstractDriver implements Interfaces\Driver {

    private $_namespace;

    /**
     * Construct method. It loads data from SESSION.
     *
     * @param string $namespace Default namespace which is user as key in storage
     *
     * @return void
     */
    public function __construct($namespace = self::DEFAULT_NAMESPACE) {
        $this->_namespace = $namespace;

        session_start();
        if (isset($_SESSION[self::SESSION_NAME][$namespace])) {
            $this->_data = unserialize($_SESSION[self::SESSION_NAME][$namespace]);
        }
    }

    /**
     * Destruct method. It save data to SESSION.
     *
     * @return void
     */
    public function __destruct() {
        if (!empty($this->_data)) {
            $_SESSION[self::SESSION_NAME][$this->_namespace] = serialize($this->_data);
        } elseif (isset($_SESSION[self::SESSION_NAME][$this->_namespace])) {
            unset($_SESSION[self::SESSION_NAME][$this->_namespace]);
        }
    }
}
