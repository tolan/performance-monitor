<?php

namespace PF\Main\Web;

use PF\Main\Provider;
use PF\Main\Access\Exception as AccessException;

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
     * @var \PF\Main\Provider
     */
    private $_provider = null;

    /**
     * Response Template
     *
     * @var \PF\Main\Web\Component\Response
     */
    private $_response = null;

    /**
     * Router instance
     *
     * @var \PF\Main\Web\Component\Router
     */
    private $_router = null;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
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
     * @return \PF\Main\Web\App
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
     * @return \PF\Main\Web\App
     */
    protected function init() {
        session_write_close();

        try {
            $this->_provider->get('access')->checkAccess();
        } catch (AccessException $exc) {
            $this->_provider->get('log')->warning('Unauthorized exception: '.$exc->getMessage());

            return $this->_showAccessDenied($exc);
        }

        $this->_response = $this->_router
            ->route()
            ->getController()
            ->run()
            ->getResponse();

        return $this;
    }

    /**
     * Method which is called before render function.
     *
     * @return \PF\Main\Web\App
     */
    protected function beforeRender() {
        $eveMan = $this->_provider->get('PF\Main\Event\Manager'); /* @var $eveMan \PF\Main\Event\Manager */
        $eveMan->flush();

        return $this;
    }

    /**
     * Render function which call flush on response instance.
     *
     * @return \PF\Main\Web\App
     */
    protected function render() {
        $this->_response->flush();

        return $this;
    }

    /**
     * Method which is called after render function.
     *
     * @return \PF\Main\Web\App
     */
    protected function afterRender() {
        $eveMan = $this->_provider->get('PF\Main\Event\Manager'); /* @var $eveMan \PF\Main\Event\Manager */
        $eveMan->flush();

        return $this;
    }

    /**
     * Forward to access denied page.
     *
     * @param \PF\Main\Access\Exception $exc Exception
     */
    private function _showAccessDenied(AccessException $exc) {
        // TODO
        return $this;
    }
}
