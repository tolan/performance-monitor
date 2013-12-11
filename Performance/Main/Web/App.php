<?php

/**
 * This script defines main class for run application.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_App {

    /**
     * Provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider = null;

    /**
     * Response Template
     *
     * @var Performance_Main_Web_Component_Response
     */
    private $_response = null;

    /**
     * Router instance
     *
     * @var Performance_Main_Web_Component_Router
     */
    private $_router = null;

    /**
     * Construct method.
     *
     * @param Performance_Main_Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Performance_Main_Provider $provider) {
        $this->_provider = $provider;
        $this->_router   = $provider->get('Performance_Main_Web_Component_Router');
    }

    /**
     * This method run application stack.
     *
     * @return Performance_Main_Web_App
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
     * @return Performance_Main_Web_App
     */
    protected function init() {
        try {
            $this->_provider->get('access')->checkAccess();
        } catch (Performance_Main_Access_Exception $exc) {
            $this->_showAccessDenied($exc);
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
     * @return Performance_Main_Web_App
     */
    protected function beforeRender() {
        return $this;
    }

    /**
     * Render function which call flush on response instance.
     *
     * @return Performance_Main_Web_App
     */
    protected function render() {
        $this->_response->flush();

        return $this;
    }

    /**
     * Method which is called after render function.
     *
     * @return Performance_Main_Web_App
     */
    protected function afterRender() {
        return $this;
    }

    private function _showAccessDenied(Performance_Main_Access_Exception $exc) {
        
    }
}
