<?php

namespace PM\Main\Web;

use PM\Main\Provider;
use PM\Main\Access\Exception as AccessException;
use PM\Main\Exception as MainException;

/**
 * This script defines main class for run application.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class App {

    /**
     * Provider instance
     *
     * @var \PM\Main\Provider
     */
    private $_provider = null;

    /**
     * Response Template
     *
     * @var \PM\Main\Web\Component\Response
     */
    private $_response = null;

    /**
     * Router instance
     *
     * @var \PM\Main\Web\Component\Router
     */
    private $_router = null;

    /**
     * Construct method.
     *
     * @param \PM\Main\Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
        $this->_router   = $provider->get('router');
    }

    /**
     * This method run application stack.
     *
     * @return \PM\Main\Web\App
     */
    final public function run() {
        $this->init();
        $this->beforeRender();
        $this->render();
        $this->afterRender();

        return $this;
    }

    /**
     * Init function for resolve routing.
     *
     * @return \PM\Main\Web\App
     */
    protected function init() {
        set_error_handler(array($this, 'errorHandler'));

        try {
            $this->_provider->get('access')->checkAccess();
        } catch (AccessException $exc) {
            $this->_provider->get('log')->warning('Unauthorized exception: '.$exc->getMessage());

            return $this->_showAccessDenied($exc);
        }

        try {
            $controller = $this->_router->getController();
            $routeInfo  = $this->_router->getRouteInfo();
        } catch (Exception $ex) {
            return $this->_showRoutingError($ex);
        }

        if (isset($routeInfo[Component\Router::ANNOTATION]) &&
            isset($routeInfo[Component\Router::ANNOTATION]['session_write_close']) &&
            $routeInfo[Component\Router::ANNOTATION]['session_write_close'] === 'false' &&
            session_id() === '') {
            session_start();
        }

        try {
            $this->_response = $controller
                ->run()
                ->getResponse();
        } catch (\Exception $exc) {
            $this->_returnApplicationError($exc);
        }

        return $this;
    }

    /**
     * Method which is called before render function.
     *
     * @return \PM\Main\Web\App
     */
    protected function beforeRender() {
        $eveMan = $this->_provider->get('PM\Main\Event\Manager'); /* @var $eveMan \PM\Main\Event\Manager */
        $eveMan->broadcast('app:'.__FUNCTION__);
        $eveMan->flush();

        return $this;
    }

    /**
     * Render function which call flush on response instance.
     *
     * @return \PM\Main\Web\App
     */
    protected function render() {
        if ($this->_response !== null) {
            $this->_response->flush();
        }

        return $this;
    }

    /**
     * Method which is called after render function.
     *
     * @return \PM\Main\Web\App
     */
    protected function afterRender() {
        $eveMan = $this->_provider->get('PM\Main\Event\Manager'); /* @var $eveMan \PM\Main\Event\Manager */
        $eveMan->broadcast('app:'.__FUNCTION__);
        $eveMan->flush();

        return $this;
    }

    /**
     * Forward to access denied page.
     *
     * @param \PM\Main\Access\Exception $exc Exception
     *
     * @return \PM\Main\Web\App
     */
    private function _showAccessDenied(AccessException $exc) {
        $this->_returnApplicationError($exc); // TODO

        return $this;
    }

    /**
     * Forward to router error page.
     *
     * @param \PM\Main\Web\Exception $exc Exception
     *
     * @return \PM\Main\Web\App
     */
    private function _showRoutingError(Exception $exc) {
        $this->_returnApplicationError($exc); // TODO

        return $this;
    }

    /**
     * Forward to application error.
     *
     * @param \PM\Main\Web\Exception $exc Exception
     *
     * @return \PM\Main\Web\App
     */
    private function _returnApplicationError(\Exception $exc) {
        $eveMan = $this->_provider->get('PM\Main\Event\Manager'); /* @var $eveMan \PM\Main\Event\Manager */
        $eveMan->broadcast('app:'.__FUNCTION__);
        $eveMan->flush();

        $template = $this->_provider->get('PM\Main\Web\Component\Template\Error'); /* @var $template \PM\Main\Web\Component\Template\Error */
        $template->setData($exc);
        $response = $this->_provider->get('response'); /* @var $response \PM\Main\Web\Component\Response */
        $response->setTemplate($template)->flush();

        $this->_provider->get('log')->error($exc);

        return $this;
    }

    /**
     * Handler for catchable error.
     *
     * @param int    $errno  Number of error
     * @param string $errstr Error message
     */
    public function errorHandler($errno, $errstr) {
        if ($errno > E_STRICT) {
            $template  = $this->_provider->get('PM\Main\Web\Component\Template\Error'); /* @var $template \PM\Main\Web\Component\Template\Error */
            $exception = new MainException($errstr);
            $template->setData($exception);
            $response = $this->_provider->get('response'); /* @var $response \PM\Main\Web\Component\Response */
            $response->setTemplate($template)->flush();

            $this->_provider->get('log')->error($exception);

            die($errstr);
        }
    }
}
