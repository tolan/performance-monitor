<?php

/**
 * This script defines class for php unit test case of class Performance_Main_Access.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class Performance_Tests_Unit_Main_AccessTest extends Performance_Main_Abstract_Unit_TestCase {

    /**
     * Access instance.
     *
     * @var Performance_Main_Access
     */
    private $_instance;

    /**
     * Server global varibale instance
     *
     * @var Performance_Main_Web_Component_Http_Server
     */
    private $_server;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_instance = $this->getProvider()->get('Performance_Main_Access');
        $this->_server   = $this->getProvider()->get('Performance_Main_Web_Component_Request')->getServer();

        $config  = $this->getProvider()->get('Performance_Main_Config'); /* @var $config Performance_Main_Config */
        $config->set('access', array(
            Performance_Main_Access_AllowFrom::CONFIG_KEY => array('192.168.1.1'),
            Performance_Main_Access_DeniedFrom::CONFIG_KEY => array('192.168.2.2')
        ));

        parent::setUp();
    }

    /**
     * Success test for check remote address and allow address.
     *
     * @return void
     */
    public function testCheckAccess() {
        $this->_server->set('REMOTE_ADDR', '192.168.1.1');

        $this->_instance->checkAccess();
    }
}
