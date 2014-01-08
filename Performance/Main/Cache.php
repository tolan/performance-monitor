<?php

namespace PF\Main;

/**
 * This script defines class for caching.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Cache implements Event\Interfaces\Sender {
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
     * Mediator instance
     *
     * @var \PF\Main\Event\Mediator
     */
    private $_mediator = null;

    /**
     * Construct method.
     *
     * @param string                  $namespace Namespace
     * @param \PF\Main\Event\Mediator $mediator  Mediator instance
     *
     * @return void
     */
    public function __construct($namespace = self::DEFAULT_NAMESPACE, Event\Mediator $mediator = null) {
        session_start();
        $this->_namespace = $namespace;
        $this->_mediator  = $mediator;

        if (isset($_SESSION[self::SESSION_NAME][$namespace])) {
            $this->_cache = unserialize($_SESSION[self::SESSION_NAME][$namespace]);
        }

        $this->send('Cache is loaded.');
    }

    /**
     * It sends message to mediator.
     *
     * @param mixed $content Message content
     *
     * @return \PF\Main\Cache
     */
    public function send($content) {
        $message = new Event\Message();
        $message->setData($content);

        if ($this->_mediator) {
            $this->_mediator->send($message, $this);
        }

        return $this;
    }

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     *
     * @throws \PF\Main\Exception Throws when variable is not defined
     */
    public function load($name) {
        if (!$this->has($name)) {
            throw new Exception('Undefined cache variable.');
        }

        return $this->_cache[$name];
    }

    /**
     * Sets value to variable by name.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Values
     *
     * @return \PF\Main\Cache
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
     * @return \PF\Main\Cache
     *
     * @throws \PF\Main\Exception Throws when variable is not set.
     */
    public function clean($name) {
        if (!$this->has($name)) {
            throw new Exception('Undefined cache variable.');
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
