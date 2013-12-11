<?php

/**
 * This script defines class for application router.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Component_Router {
    const CONTROLLER = 'controller';
    const METHOD     = 'action';
    const PARAMS     = 'params';

    /**
     * Http request
     *
     * @var Performance_Main_Web_Component_Request
     */
    private $_request;

    /**
     * Provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * Controller instance
     *
     * @var Performance_Main_Web_Controller_Abstract
     */
    private $_controller = null;

    /**
     * Construct method
     *
     * @param Performance_Main_Provider              $provider Provider instnace
     * @param Performance_Main_Web_Component_Request $request  Request instance
     */
    public function __construct(Performance_Main_Provider $provider, Performance_Main_Web_Component_Request $request) {
        $this->_provider = $provider;
        $this->_request  = $request;
    }

    /**
     * Resolve route and create controller instance.
     *
     * @return Performance_Main_Web_Component_Router
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
     * @return Performance_Main_Web_Controller_Abstract
     */
    public function getController() {
        return $this->_controller;
    }

    /**
     * This method resolve route and return controller instance.
     *
     * @return Performance_Main_Web_Controller_Abstract
     */
    private function _resolveController() {
        $server = $this->_request->getServer();

        $path = substr($server->getREQUEST_URI(), strlen($server->getBASE()));

        $route = $this->_resolveNewRoute($path);

        $controller = $this->_provider->get('Performance_Main_Web_Controller_'.$route[self::CONTROLLER]);
        $controller->setAction($route[self::METHOD]);
        $controller->setParams($route[self::PARAMS]);

        return $controller;
    }

    /**
     * Resolve routing parameters.
     *
     * @param string $path Input request path
     *
     * @return array
     *
     * @throws Performance_Main_Web_Exception Throws when controller has wrong base path.
     */
    private function _resolveNewRoute($path) {
        list($controller, $action) =  explode('/', trim($path, '/').'/', 2);

        $controller = $controller === '' ? 'Homepage' : $controller;

        $class = 'Performance_Main_Web_Controller_'.ucfirst($controller);

        $classAnnot = $this->_getClassAnnotations($class);

        if (isset($classAnnot['link']) && strpos($path, $classAnnot['link']) === false) {
            throw new Performance_Main_Web_Exception('Route doesn\'t match.');
        }

        $classMethodsAnnot = $this->_getClassMethodsAnnotations($class);

        $requestMethod = $this->_request->getServer()->getREQUEST_METHOD();

        $method = null;
        $params = array();

        foreach ($classMethodsAnnot as $methodName => $annot) {
            if (isset($annot['link'])) {
                $regExpMethod = preg_replace('#\{[a-zA-Z]+\}#', '[a-zA-Z0-9-]+', $annot['link']);
                $regExp = '#/'.$controller.'/'.ltrim($regExpMethod, '/').'#';
                if (preg_match($regExp, $path) && ((isset($annot['method']) && $requestMethod == $annot['method']) || !isset($annot['method']))) {
                    $method = substr($methodName, strlen('action'));
                    $params = $this->_getPathParams($action, $annot['link']);
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
            self::CONTROLLER => $controller,
            self::METHOD     => $method,
            self::PARAMS     => $params
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
        $path = trim($path, '/');
        $link = trim($link, '/');

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
     * Returns annotation from all acion methods in controller class.
     *
     * @param string $class Class name
     *
     * @return array Array with annotations
     */
    private function _getClassMethodsAnnotations($class) {
        $refl = new ReflectionClass($class);
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
     */
    private function _getClassAnnotations($class) {
        $annotations = array();

        $refl = new ReflectionClass($class);
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
        $routeParams = array(
            self::CONTROLLER => is_null($pathParams[self::CONTROLLER]) ? 'Homepage' : ucfirst($pathParams[self::CONTROLLER]),
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
}