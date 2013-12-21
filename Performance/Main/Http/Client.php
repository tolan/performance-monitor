<?php

/**
 * This script defines class for http request client.
 * It provides managing of http requests for sending multiple requests with one cookies (it stores php session id).
 *
 * Example usage:
 *
 * $cookie = new Performance_Main_Http_Cookies();
 * $client = new Performance_Main_Http_Client($cookie)
 *
 * $params = array(
 *     '0' => array(
 *         'name' => 'test',
 *         'value' => 'aaa',
 *         'method' => 'GET'
 *     )
 * );
 * $request = $client->createRequest(Performance_Main_Http_Enum_Method::GET, 'http://perf.lc/test.php', $params);
 * $client->addRequest($request);
 * $client->addRequest($request);
 * $client->addRequest($request);
 * $client->send();
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Http_Client {

    /**
     * Cookies instance for sharing one cookies over all requests.
     *
     * @var Performance_Main_Http_Cookies
     */
    private $_cookies = null;

    /**
     * Stack for requests.
     *
     * @var array
     */
    private $_requests = array();

    /**
     * Construct method.
     *
     * @param Performance_Main_Http_Cookies $cookies Cookies instance
     */
    public function __construct(Performance_Main_Http_Cookies $cookies) {
        $this->_cookies = $cookies;
    }

    /**
     * It adds one request to stack and sets cookies.
     *
     * @param Performance_Main_Http_Request_Abstract $request Request instance
     *
     * @return Performance_Main_Http_Client
     */
    public function addRequest(Performance_Main_Http_Request_Abstract $request) {
        $request->setCookieJar($this->_cookies);
        $this->_requests[] = $request;

        return $this;
    }

    /**
     * It removes request from stack.
     *
     * @param Performance_Main_Http_Request_Abstract $request Request instance
     *
     * @return Performance_Main_Http_Client
     */
    public function removeRequest(Performance_Main_Http_Request_Abstract $request) {
        foreach ($this->_requests as $key => $req) {
            if ($req === $request) {
                unset($this->_requests[$key]);
            }
        }

        return $this;
    }

    /**
     * It creates and returns request intance by given method, url and parameters.
     *
     * @param enum   $method     One of Performance_Main_Http_Enum_Method
     * @param string $url        Url of target address
     * @param array  $parameters Array with parameters as POST, GET, etc.
     *
     * @return Performance_Main_Http_Request_Abstract
     */
    public function createRequest($method, $url, $parameters = array()) {
        $request = Performance_Main_Http_Request_Factory::getInstance($method);
        $url     = preg_match('#^https*://#', $url) ? $url : 'http://' . $url;
        $request->setUrl($url);
        $this->_addParameters($request, $parameters);

        return $request;
    }

    /**
     * It adds parameters to given request.
     *
     * @param Performance_Main_Http_Request_Abstract $request    Request instance
     * @param array                                  $parameters Array with parameters as POST, GET, etc.
     *
     * @return Performance_Main_Http_Request_Abstract
     *
     * @throws Performance_Main_Http_Exception Throws when parameters is not allowed on request type.
     */
    private function _addParameters(Performance_Main_Http_Request_Abstract $request, $parameters = array()) {
        $allowedParams = Performance_Main_Http_Enum_ParameterType::getAllowedParams($request->getMethod());

        foreach ($parameters as $data) {
            if (in_array($data['method'], $allowedParams)) {
                $this->_addMethodParameters($request, $data['method'], $data);
            } else {
                throw new Performance_Main_Http_Exception('Method parameter '.$data['method'].' is not allowed on request '.$request->getMethod().'.');
            }
        }

        return $request;
    }

    /**
     * It adds concrete parameter to request.
     *
     * @param Performance_Main_Http_Request_Abstract $request Request instance
     * @param enum                                   $method  One of Performance_Main_Http_Enum_ParameterType
     * @param array                                  $data    Array with parameters as method, name and value
     *
     * @return Performance_Main_Http_Request_Abstract
     */
    private function _addMethodParameters(Performance_Main_Http_Request_Abstract $request, $method, $data) {
        switch ($method) {
            case Performance_Main_Http_Enum_ParameterType::GET:
                $url = $request->getUrl(); /* @var $url Net_URL2 */
                $url->setQueryVariable($data['name'], $data['value']);
                break;
            case Performance_Main_Http_Enum_ParameterType::POST:
                $request->addPostParameter($data['name'], $data['value']);
        }

        return $request;
    }

    /**
     * Method for send all request in stack.
     *
     * @return Performance_Main_Http_Client
     */
    public function send() {
        foreach ($this->_requests as $request) { /* @var $request Performance_Main_Http_Request_Abstract */
            $request->send();
        }

        return $this;
    }
}