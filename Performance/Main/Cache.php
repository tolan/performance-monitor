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

    const DEFAULT_NAMESPACE = 'Performance';

    /**
     * Cache driver.
     *
     * @var Cache\Interfaces\Driver
     */
    private $_driver;

    /**
     * Mediator instance
     *
     * @var \PF\Main\Event\Mediator
     */
    private $_mediator = null;

    /**
     * Construct method.
     *
     * @param \Cache\Interfaces\Driver $driver    Cache driver
     * @param string                   $namespace Namespace
     * @param \PF\Main\Event\Mediator  $mediator  Mediator instance
     *
     * @return void
     */
    public function __construct($driver = null, $namespace = self::DEFAULT_NAMESPACE, Event\Mediator $mediator = null) {
        if ($driver && !($driver instanceof Cache\Interfaces\Driver)) {
            throw new Exception('Cache driver must be instance of Cache\Interfaces\Driver.');
        } elseif (!$driver) {
            $driver = new Cache\Session($namespace);
        }

        $this->_driver    = $driver;
        $this->_mediator  = $mediator;

        $this->send('Cache is loaded.');
    }

    public function setDriver(Cache\Interfaces\Driver $driver) {
        $this->_driver = $driver;

        return $this;
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
     * @return \PF\Main\Cache
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
     * @return \PF\Main\Cache
     */
    public function clean($name = null) {
        $this->_driver->clean($name);

        return $this;
    }
}
