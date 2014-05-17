<?php

namespace PF\Main\Web\Component;

/**
 * This script defines class for input request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Request {

    /**
     * Stack for instances
     *
     * @var array
     */
    private $_instances = array();

    /**
     * Cache for input of PHP.
     *
     * @var mixed
     */
    private $_input = null;

    /**
     * Return php input decoded from JSON
     *
     * @return mixed
     */
    public function getInput() {
        if ($this->_input === null) {
            $this->_input = json_decode(file_get_contents('php://input'), true);
        }

        return $this->_input;
    }

    /**
     * Return entity instance with COOKIE.
     *
     * @return \PF\Main\Web\Component\Http\Cookie
     */
    public function getCookie() {
        return $this->_getInstance('Cookie');
    }

    /**
     * Return entity instance with ENV.
     *
     * @return \PF\Main\Web\Component\Http\Env
     */
    public function getEnv() {
        return $this->_getInstance('Env');
    }

    /**
     * Return entity instance with FILES.
     *
     * @return \PF\Main\Web\Component\Http\Files
     */
    public function getFiles() {
        return $this->_getInstance('Files');
    }

    /**
     * Return entity instance with GET.
     *
     * @return \PF\Main\Web\Component\Http\Get
     */
    public function getGet() {
        return $this->_getInstance('Get');
    }

    /**
     * Return entity instance with POST.
     *
     * @return \PF\Main\Web\Component\Http\Post
     */
    public function getPost() {
        return $this->_getInstance('Post');
    }

    /**
     * Return entity instance with REQUEST.
     *
     * @return \PF\Main\Web\Component\Http\Request
     */
    public function getRequest() {
        return $this->_getInstance('Request');
    }

    /**
     * Return entity instance with SERVER.
     *
     * @return \PF\Main\Web\Component\Http\Server
     */
    public function getServer() {
        return $this->_getInstance('Server');
    }

    /**
     * Return entity instance with SESSION.
     *
     * @return \PF\Main\Web\Component\Http\Session
     */
    public function getSession() {
        return $this->_getInstance('Session');
    }

    /**
     * Returns all instance of whole request.
     *
     * @return array
     */
    public function getAll() {
        $refl = new \ReflectionClass(get_class());
        $reflMethods = $refl->getMethods();
        $result = array();

        foreach ($reflMethods as $method) {
            if (strpos($method->getName(), 'get') === 0 && $method->getName() !== __FUNCTION__) {
                $var = strtoupper(substr($method->getName(), 3));
                $result[$var] = $this->{$method->getName()}();
            }
        }

        return $result;
    }

    /**
     * Returns instance of http web component (some instance of global variable)
     *
     * @param string $name Class name of http component
     *
     * @return \PF\Main\Web\Component\Http\AbstractHttp
     */
    private function _getInstance($name) {
        if (!isset($this->_instances[$name])) {
            $class = '\\'.__NAMESPACE__.'\\Http\\'.$name;
            $this->_instances[$name] = new $class();
        }

        return $this->_instances[$name];
    }
}