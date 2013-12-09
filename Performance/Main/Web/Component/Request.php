<?php

/**
 * This script defines class for input request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Component_Request {

    /**
     * Stack for instances
     *
     * @var array
     */
    private $_instances = array();

    /**
     * Return php input decoded from JSON
     *
     * @return mixed
     */
    public function getInput() {
        return json_decode(file_get_contents('php://input'), true);
    }

    /**
     * Return entity instance with COOKIE.
     *
     * @return Performance_Main_Web_Component_Http_Cookie
     */
    public function getCookie() {
        return $this->_getInstance('Cookie');
    }

    /**
     * Return entity instance with ENV.
     *
     * @return Performance_Main_Web_Component_Http_Env
     */
    public function getEnv() {
        return $this->_getInstance('Env');
    }

    /**
     * Return entity instance with FILES.
     *
     * @return Performance_Main_Web_Component_Http_Files
     */
    public function getFiles() {
        return $this->_getInstance('Files');
    }

    /**
     * Return entity instance with GET.
     *
     * @return Performance_Main_Web_Component_Http_Get
     */
    public function getGet() {
        return $this->_getInstance('Get');
    }

    /**
     * Return entity instance with POST.
     *
     * @return Performance_Main_Web_Component_Http_Post
     */
    public function getPost() {
        return $this->_getInstance('Post');
    }

    /**
     * Return entity instance with REQUEST.
     *
     * @return Performance_Main_Web_Component_Http_Request
     */
    public function getRequest() {
        return $this->_getInstance('Request');
    }

    /**
     * Return entity instance with SERVER.
     *
     * @return Performance_Main_Web_Component_Http_Server
     */
    public function getServer() {
        return $this->_getInstance('Server');
    }

    /**
     * Return entity instance with SESSION.
     *
     * @return Performance_Main_Web_Component_Http_Session
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
        $refl = new ReflectionClass(get_class());
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
     * @return Performance_Main_Web_Component_Http_Abstract
     */
    private function _getInstance($name) {
        if (!isset($this->_instances[$name])) {
            $class = 'Performance_Main_Web_Component_Http_'.$name;
            $this->_instances[$name] = new $class();
        }

        return $this->_instances[$name];
    }
}