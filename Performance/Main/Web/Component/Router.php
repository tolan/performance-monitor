<?php

namespace PM\Main\Web\Component;

use PM\Main\Provider;
use PM\Main\Web\Exception;

/**
 * This script defines class for application router.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Router {

    const CONTROLLER = 'controller';
    const METHOD     = 'action';
    const PARAMS     = 'params';
    const ANNOTATION = 'annotation';

    /**
     * Http request
     *
     * @var \PM\Main\Web\Component\Request
     */
    private $_request;

    /**
     * Provider instance
     *
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * Controller instance
     *
     * @var \PM\Main\Web\Controller\Abstracts\Controller
     */
    private $_controller = null;

    /**
     * There is stored routing information.
     *
     * @var array
     */
    private $_routeInfo = array();

    /**
     * Construct method
     *
     * @param \PM\Main\Provider              $provider Provider instnace
     * @param \PM\Main\Web\Component\Request $request  Request instance
     */
    public function __construct(Provider $provider, Request $request) {
        $this->_provider = $provider;
        $this->_request  = $request;
    }

    /**
     * Resolve route and create controller instance.
     *
     * @return \PM\Main\Web\Component\Router
     */
    public function route() {
        if ($this->_controller === null) {
            $this->_controller = $this->_resolveController();
        }

        return $this;
    }

    /**
     * Returns instance of controller.
     *
     * @return \PM\Main\Web\Controller\Abstracts\Controller
     */
    public function getController() {
        $this->route();

        return $this->_controller;
    }

    /**
     * Returns information about routing process.
     *
     * @return array
     */
    public function getRouteInfo() {
        $this->route();

        return $this->_routeInfo;
    }

    /**
     * This method resolve route and return controller instance.
     *
     * @return \PM\Main\Web\Controller\Abstracts\Controller
     */
    private function _resolveController() {
        $server = $this->_request->getServer();

        if($server->hasBASE() === false) {
            $server->setBASE('');
        }

        $path = substr($server->getREQUEST_URI(), strlen($server->getBASE()));

        $getParams = array();
        if (strpos($path, '?')) {
            $getParams = $this->_parseGetParams($path);
            $path      = $getParams['path'];
        }

        $route = $this->_resolveNewRoute($path);

        if (isset($getParams['params'])) {
            foreach ($getParams['params'] as $key => $value) {
                $route[self::PARAMS][$key] = $value;
            }
        }

        $controller = $this->_provider->get($route[self::CONTROLLER]);
        $controller->setAction($route[self::METHOD]);
        $controller->setParams($route[self::PARAMS]);

        $this->_routeInfo = $route;

        return $controller;
    }

    /**
     * Resolve routing parameters.
     *
     * @param string $path Input request path
     *
     * @return array
     *
     * @throws \PM\Main\Web\Exception Throws when controller has wrong base path.
     */
    private function _resolveNewRoute($path) {
        list($module, $controller, $action) =  explode('/', trim($path, '/').'/', 3);

        if (!$action) {
            $action     = $controller;
            $controller = $module;
            $module     = 'Main';
        }

        $module     = $module ? ucfirst($module) : $module;
        $controller = $controller === '' ? 'Homepage' : $controller;

        $class = '\PM\\'.$module.'\Web\Controller\\'.ucfirst($controller);

        $classAnnot = $this->_getClassAnnotations($class);

        if (isset($classAnnot['link']) && strpos($path, $classAnnot['link']) === false) {
            throw new Exception('Route doesn\'t match.');
        }

        $classMethodsAnnot = $this->_getClassMethodsAnnotations($class);

        $requestMethod = $this->_request->getServer()->getREQUEST_METHOD();

        $method     = null;
        $params     = array();
        $annotation = array();

        foreach ($classMethodsAnnot as $methodName => $annot) {
            if (isset($annot['link'])) {
                $regExpMethod = preg_replace('#\{[a-zA-Z]+\}#', '[a-zA-Z0-9-]+', $annot['link']);
                $regExp = '#/'.$controller.'/'.ltrim($regExpMethod, '/').'#';
                if (preg_match($regExp, $path) && ((isset($annot['method']) && $requestMethod == $annot['method']) || !isset($annot['method']))) {
                    $method     = substr($methodName, strlen('action'));
                    $params     = $this->_getPathParams($action, $annot['link']);
                    $annotation = $annot;
                }
            }
        }

        if ($method === null) {
            $resolvedPath  = $this->_resolvePath($path);
            $resolverRoute = $this->_resolveRoute($resolvedPath);
            $controller    = $resolverRoute[self::CONTROLLER];
            $method        = $resolverRoute[self::METHOD];
            $params        = $resolverRoute[self::PARAMS];
        }

        $routeParams = array(
            self::CONTROLLER => $class,
            self::METHOD     => $method,
            self::PARAMS     => $params,
            self::ANNOTATION => $annotation
        );

        return $routeParams;
    }

    /**
     * Returns parameters from match path and link (it is defines by {}).
     *
     * @param string $path Input request path
     * @param string $link Link method in controller action
     *
     * @return array Array with parameters (like as array('id' => '2')
     */
    private function _getPathParams($path, $link) {
        $path = '/'.trim($path, '/');
        $link = '/'.trim($link, '/');

        $result = null;
        while(strpos($link, '{')) {
            $prefix = strstr($link, '{', true);
            $key    = strstr(substr($link, strlen($prefix) + 1), '}', true);
            $link   = substr($link, strlen($prefix.'{'.$key.'}'));

            $path   = substr($path, strlen($prefix));
            $value  = strpos($path, '/') ? strstr($path, '/', true) : $path;
            $path   = substr($path, strlen($value));
            $result[$key] = $value;
        }

        return $result;
    }

    /**
     * Returns annotation from all action methods in controller class.
     *
     * @param string $class Class name
     *
     * @return array Array with annotations
     */
    private function _getClassMethodsAnnotations($class) {
        $refl = new \ReflectionClass($class);
        $methods = $refl->getMethods();

        $annotations = array();
        $result = array();

        foreach ($methods as $method) {
            $doc = $method->getDocComment();
            preg_match_all('#@(.*?)\n#s', $doc, $annotations[$method->getName()]);

            foreach ($annotations[$method->getName()][1] as $annot) {
                list($key, $value) = explode(' ', $annot, 3);
                $result[$method->getName()][$key] = trim($value);
            }
        }

        return $result;
    }

    /**
     * Gets annotations from class definition.
     *
     * @param string $class Class name
     *
     * @return array Array with annotation
     *
     * @throws \PM\Main\Web\Exception Throws when controller doesn't exist.
     */
    private function _getClassAnnotations($class) {
        $annotations = array();

        if (class_exists($class) === false) {
            throw new Exception('Routing error. Controller cannot be resolved: '.$class.'.');
        }

        $refl = new \ReflectionClass($class);
        $doc  = $refl->getDocComment();

        preg_match_all('#@(.*?)\n#s', $doc, $annotations);

        $result = array();

        foreach ($annotations[1] as $annot) {
            list($key, $value) = explode(' ', $annot, 3);
            $result[$key] = trim($value);
        }

        return $result;
    }

    /**
     * Resolve route parameters from path parameters.
     *
     * @param array $pathParams Path parameters
     *
     * @return array Route parameters
     */
    private function _resolveRoute($pathParams) {
        $controller = null;
        if (is_null($pathParams[self::CONTROLLER])) {
            $controller = 'PM\Main\Web\Controller\Homepage';
        } else {
            $controller = 'PM\\'.ucfirst($pathParams[self::CONTROLLER]).'\Web\Controller\\'.ucfirst($pathParams[self::CONTROLLER]);
        }

        $routeParams = array(
            self::CONTROLLER => $controller,
            self::METHOD     => is_null($pathParams[self::METHOD])     ? 'Index' : ucfirst($pathParams[self::METHOD]),
            self::PARAMS     => is_null($pathParams[self::PARAMS])     ? null : $pathParams[self::PARAMS]
        );

        return $routeParams;
    }

    /**
     * Resolve path parameters from input request path.
     *
     * @param string $path Input request path
     *
     * @return array Array with path parameters
     */
    private function _resolvePath($path) {
        $pathParams = array(
            self::CONTROLLER => null,
            self::METHOD     => null,
            self::PARAMS     => null
        );
        $path = explode('/', ltrim($path, '/'), 3);

        $pathParams[self::CONTROLLER] = empty($path[0]) ? null : $path[0];

        if (isset($path[1]) && !empty($path[1])) {
            $pathParams[self::METHOD] = $path[1];
        }

        if (isset($path[2]) && !empty($path[2])) {
            $params = explode('/', $path[2], 2);
            $pathParams[self::PARAMS] = array(
                $params[0] => isset($params[1]) ? $params[1] : null
            );
        }

        return $pathParams;
    }

    /**
     * It provides parsing of get params and returns array with values.
     *
     * @param string $path Input request path
     *
     * @return array
     */
    private function _parseGetParams($path) {
        $params = explode('&', ltrim(strstr($path, '?'), '?'));
        $result = array();
        foreach ($params as $param) {
            list($key, $value)      = explode('=', $param);
            $result['params'][$key] = $value;
        }

        $result['path'] = strstr($path, '?', true);

        return $result;
    }
}
