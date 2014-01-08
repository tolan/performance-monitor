<?php

namespace PF\Tests\Unit\Main;

use PF\Main\Abstracts\Unit\TestCase;
use PF\Main\Access;

/**
 * This script defines class for php unit test case of class \PF\Main\Access.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class AccessTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Access
     */
    private $_instance;

    /**
     * Server global varibale instance
     *
     * @var \PF\Main\Web\Component\Http\Server
     */
    private $_server;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_instance = $this->getProvider()->get('PF\Main\Access');
        $this->_server   = $this->getProvider()->get('PF\Main\Web\Component\Request')->getServer();

        $config  = $this->getProvider()->get('PF\Main\Config'); /* @var $config \PF\Main\Config */
        $config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array('192.168.1.1'),
            Access\DeniedFrom::CONFIG_KEY => array('192.168.2.2')
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
