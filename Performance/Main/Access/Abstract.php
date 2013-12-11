<?php

abstract class Performance_Main_Access_Abstract {

    /**
     *
     * @var Performance_Main_Web_Component_Request
     */
    private $_request;

    /**
     *
     * @var Performance_Main_Config
     */
    private $_config;

    final public function __construct(Performance_Main_Web_Component_Request $request, Performance_Main_Config $config) {
        $this->_request = $request;
        $this->_config  = $config;
    }

    final protected function getConfig() {
        return $this->_config->get('access');
    }

    final protected function getServer() {
        return $this->_request->getServer();
    }

    abstract public function checkAccess();
}
