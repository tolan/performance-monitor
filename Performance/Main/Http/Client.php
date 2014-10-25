<?php

namespace PM\Main\Http;

/**
 * This script defines class for http request client.
 * It provides managing of http requests for sending multiple requests with one cookies (it stores php session id).
 *
 * Example usage:
 *
 * $cookie = new \PM\Main\Http\Cookies();
 * $client = new \PM\Main\Http\Client($cookie);
 *
 * $params = array(
 *     '0' => array(
 *         'name' => 'test',
 *         'value' => 'aaa',
 *         'method' => 'GET'
 *     )
 * );
 * $request = $client->createRequest(\PM\Main\Http\Enum\Method::GET, 'http://perf.lc/test.php', $params);
 * $client->addRequest($request);
 * $client->addRequest($request);
 * $client->addRequest($request);
 * $client->send();
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Client {

    /**
     * Cookies instance for sharing one cookies over all requests.
     *
     * @var \PM\Main\Http\Cookies
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
     * @param \PM\Main\Http\Cookies $cookies Cookies instance
     */
    public function __construct(Cookies $cookies) {
        $this->_cookies = $cookies;
    }

    /**
     * It adds one request to stack and sets cookies.
     *
     * @param \PM\Main\Http\Request\AbstractRequest $request Request instance
     *
     * @return \PM\Main\Http\Client
     */
    public function addRequest(Request\AbstractRequest $request) {
        $request->setCookieJar($this->_cookies);
        $this->_requests[] = $request;

        return $this;
    }

    /**
     * It removes request from stack.
     *
     * @param \PM\Main\Http\Request\AbstractRequest $request Request instance
     *
     * @return \PM\Main\Http\Client
     */
    public function removeRequest(Request\AbstractRequest $request) {
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
     * @param enum   $method     One of \PM\Main\Http\Enum\Method
     * @param string $url        Url of target address
     * @param array  $parameters Array with parameters as POST, GET, etc.
     *
     * @return \PM\Main\Http\Request\AbstractRequest
     */
    public function createRequest($method, $url, $parameters = array()) {
        $request = Request\Factory::getInstance($method);
        $url     = preg_match('#^https*://#', $url) ? $url : 'http://' . $url;
        $request->setUrl($url);
        $this->_addParameters($request, $parameters);

        return $request;
    }

    /**
     * It adds parameters to given request.
     *
     * @param \PM\Main\Http\Request\AbstractRequest $request    Request instance
     * @param array                                 $parameters Array with parameters as POST, GET, etc.
     *
     * @return \PM\Main\Http\Request\AbstractRequest
     *
     * @throws \PM\Main\Http\Exception Throws when parameters is not allowed on request type.
     */
    private function _addParameters(Request\AbstractRequest $request, $parameters = array()) {
        $allowedParams = Enum\ParameterType::getAllowedParams($request->getMethod());

        foreach ($parameters as $data) {
            if (in_array($data['method'], $allowedParams)) {
                $this->_addMethodParameters($request, $data['method'], $data);
            } else {
                throw new Exception('Method parameter '.$data['method'].' is not allowed on request '.$request->getMethod().'.');
            }
        }

        return $request;
    }

    /**
     * It adds concrete parameter to request.
     *
     * @param \PM\Main\Http\Request\AbstractRequest $request Request instance
     * @param enum                                  $method  One of \PM\Main\Http\Enum\ParameterType
     * @param array                                 $data    Array with parameters as method, name and value
     *
     * @return \PM\Main\Http\Request\AbstractRequest
     */
    private function _addMethodParameters(Request\AbstractRequest $request, $method, $data) {
        switch ($method) {
            case Enum\ParameterType::GET:
                $url = $request->getUrl(); /* @var $url Net_URL2 */
                $url->setQueryVariable($data['name'], $data['value']);
                break;
            case Enum\ParameterType::POST:
                $request->addPostParameter($data['name'], $data['value']);
        }

        return $request;
    }

    /**
     * Method for send all request in stack.
     *
     * @return \PM\Main\Http\Client
     */
    public function send() {
        foreach ($this->_requests as $request) { /* @var $request \PM\Main\Http\Request\AbstractRequest */
            $request->send();
        }

        return $this;
    }
}