<?php

namespace PF\Main;

/**
 * This script defines class for application configuration.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Config {

    const NAME_SPACE = 'PF';

    /**
     * Configuration data
     *
     * @var array
     */
    private $_data = array();

    /**
     * Config instance
     *
     * @var \PF\Main\Config
     */
    private static $_instance = null;

    /**
     * Construct method for init default values.
     */
    private function __construct() {
        $this->set('root', dirname(__DIR__));
        $this->set('namespace', self::NAME_SPACE);
    }

    /**
     * Returns singleton instance.
     *
     * @return \PF\Main\Config
     */
    public static function getInstance() {
        if (self::$_instance === null) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * It loads configuration from JSON file.
     *
     * @param string $configFile Path to config file.
     *
     * @return \PF\Main\Config
     *
     * @throws \PF\Main\Exception Throws when config file doesn't exist.
     */
    public function loadJson($configFile) {
        if (file_exists($configFile)) {
            $content = file_get_contents($configFile);

            $config = json_decode($content, JSON_OBJECT_AS_ARRAY);
            $this->fromArray($config);
        } else {
            throw new Exception('Config file doesn\'t exist.');
        }

        return $this;
    }

    /**
     * Magic method for basic function (set, get).
     *
     * @param string $name      Method name
     * @param array  $arguments Input data
     *
     * @throws \PF\Main\Exception Throws when called function doesn't exists.
     *
     * @return mixed
     */
    public function __call($name, $arguments) {
        $method = substr($name, 0, 3);
        $property = substr($name, 3);

        switch ($method) {
            case 'set':
                return $this->set($property, $arguments[0]);
            case 'get':
                return $this->get($property);
            default:
                throw new Exception('Undefined function');
        }
    }

    /**
     * This method load configuration data from array
     *
     * @param array $array Configuration data
     *
     * @return \PF\Main\Config
     */
    public function fromArray(array $array = null) {
        foreach ($array as $key => $item) {
            $this->set($key, $item);
        }

        return $this;
    }

    /**
     * This return all configuration data.
     *
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }

    /**
     * Sets configuration option
     *
     * @param string $name Name of option
     * @param mixed  $data Configuration data
     *
     * @return \PF\Main\Config
     */
    public function set($name, $data) {
        $this->_data[lcfirst($name)] = $data;

        return $this;
    }

    /**
     * Returns configuration option by name.
     *
     * @param string $name    Name of configuration option
     * @param mixed  $default Default value when config is not defined
     *
     * @return mixed Configuration data
     *
     * @throws \PF\Main\Exception Throws when option is not defined.
     */
    public function get($name, $default=null) {
        return array_key_exists(lcfirst($name), $this->_data) ? $this->_data[lcfirst($name)] : $default;
    }

    /**
     * This method unset configuration data. When name is null then it clear all configuration.
     *
     * @param string $name Name of configuration option
     *
     * @return \PF\Main\Config
     *
     * @throws \PF\Main\Exception Throws when option is not defined.
     */
    public function reset($name=null) {
        if($name === null) {
            $this->_data = array();
        } elseif($this->hasOwnProperty($name)) {
            unset($this->_data[lcfirst($name)]);
        } else {
            throw new Exception('Property "'.lcfirst($name).'" is not defined.');
        }

        return $this;
    }

    /**
     * This checks that option is defined.
     *
     * @param string $name Name of configuration option
     *
     * @return boolean TRUE when option is defined
     */
    public function hasOwnProperty($name) {
        return key_exists(lcfirst($name), $this->_data);
    }
}
