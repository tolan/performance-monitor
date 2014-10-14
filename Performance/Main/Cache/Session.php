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
     * Flag for fully loaded data from session.
     *
     * @var boolean
     */
    private $_loaded = false;

    /**
     * Construct method. It loads data from SESSION.
     *
     * @param string $namespace Default namespace which is user as key in storage
     *
     * @return void
     */
    public function __construct($namespace = self::DEFAULT_NAMESPACE) {
        $this->_namespace = $namespace;
    }

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     *
     * @throws \PF\Main\Cache\Exception Throws when variable is not defined
     */
    public function load($name = null) {
        $this->_loadSessionData();

        return parent::load($name);
    }

    /**
     * Returns that variable is set.
     *
     * @param string $name Name of variable
     *
     * @return boolean
     */
    public function has($name) {
        $this->_loadSessionData();

        return parent::has($name);
    }

    /**
     * Destruct method. It save data to SESSION.
     *
     * @return void
     */
    public function __destruct() {
        if (!empty($this->_data)) {
            $_SESSION[self::SESSION_NAME][$this->_namespace] = $this->_data;
        } elseif (isset($_SESSION[self::SESSION_NAME][$this->_namespace])) {
            unset($_SESSION[self::SESSION_NAME][$this->_namespace]);
        }
    }

    /**
     * Loads data from session.
     *
     * @return \PF\Main\Cache\Session
     */
    private function _loadSessionData() {
        if ($this->_loaded === false) {
            if (session_id() === '') {
                session_start();
            }

            if (isset($_SESSION[self::SESSION_NAME][$this->_namespace])) {
                $this->_data   = $_SESSION[self::SESSION_NAME][$this->_namespace];
                $this->_loaded = true;
            }
        }

        return $this;
    }
}
