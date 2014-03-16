<?php

namespace PF\Main\Cache;

class Session extends AbstractCache implements Interfaces\Driver {

    private $_namespace;

    public function __construct($namespace = self::DEFAULT_NAMESPACE) {
        $this->_namespace = $namespace;

        session_start();
        if (isset($_SESSION[self::SESSION_NAME][$namespace])) {
            $this->_data = unserialize($_SESSION[self::SESSION_NAME][$namespace]);
        }
    }

    public function flush() {
        if (!empty($this->_data)) {
            $_SESSION[self::SESSION_NAME][$this->_namespace] = serialize($this->_data);
        } elseif (isset($_SESSION[self::SESSION_NAME][$this->_namespace])) {
            unset($_SESSION[self::SESSION_NAME][$this->_namespace]);
        }

        return $this;
    }
}
