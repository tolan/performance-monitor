<?php

namespace PM\Main;

/**
 * This script defines class for application configuration.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Config {

    const NAME_SPACE = 'PM';

    /**
     * Configuration data
     *
     * @var array
     */
    private $_data = array();

    /**
     * List of last modification times for each configuration option
     *
     * @var array
     */
    private $_configTimes = array();

    /**
     * Config instance
     *
     * @var Config
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
     * @return Config
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
     * @return Config
     *
     * @throws Exception Throws when config file doesn't exist.
     */
    public function loadJson($configFile) {
        if (file_exists($configFile)) {
            $content = file_get_contents($configFile);
            $config  = json_decode($content, JSON_OBJECT_AS_ARRAY);

            $times = array();
            $mtime = filemtime($configFile);
            foreach (array_keys($config) as $key) {
                $times[$key] = $mtime;
            }

            $this->_configTimes = array_merge($this->_configTimes, $times);

            $this->fromArray($config);
        } else {
            throw new Exception('Config file doesn\'t exist.');
        }

        return $this;
    }

    /**
     * Returns last modification time of configuration option.
     *
     * @param string $key Key of option
     *
     * @return int
     */
    public function getOptionTime($key) {
        return array_key_exists($key, $this->_configTimes) ? $this->_configTimes[$key] : -1;
    }

    /**
     * Magic method for basic function (set, get).
     *
     * @param string $name      Method name
     * @param array  $arguments Input data
     *
     * @throws Exception Throws when called function doesn't exists.
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
     * @return Config
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
     * @return Config
     */
    public function set($name, $data) {
        $key = lcfirst($name);

        if (array_key_exists($key, $this->_data) && is_array($this->_data[$key])) {
            $this->_data[$key] = array_unique(
                array_merge($this->_data[$key], (array)$data),
                SORT_REGULAR
            );
        } else {
            $this->_data[$key] = $data;
        }

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
     * @throws Exception Throws when option is not defined.
     */
    public function get($name, $default=null) {
        return array_key_exists(lcfirst($name), $this->_data) ? $this->_data[lcfirst($name)] : $default;
    }

    /**
     * This method unset configuration data. When name is null then it clear all configuration.
     *
     * @param string $name Name of configuration option
     *
     * @return Config
     *
     * @throws Exception Throws when option is not defined.
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
