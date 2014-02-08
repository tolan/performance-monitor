<?php

namespace PF\Tests\Unit\Main\Access;

use PF\Main\Abstracts\Unit\TestCase;
use PF\Main\Access;

/**
 * This script defines class for php unit test case of class \PF\Main\Access\AllowFrom.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class AllowFromTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Access\AllowFrom
     */
    private $_instance;

    /**
     * Server global varibale instance
     *
     * @var \PF\Main\Web\Component\Http\Server
     */
    private $_server;

    /**
     *
     * @var \PF\Main\Config
     */
    private $_config;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_instance = $this->getProvider()->get('PF\Main\Access\AllowFrom');
        $this->_server   = $this->getProvider()->get('PF\Main\Web\Component\Request')->getServer();
        $this->_config   = $this->getProvider()->get('PF\Main\Config');
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.64.0.0/17',
                '10.96.0.0/16',
                '172.16.128.0/16',
                '192.168.0.1/24'
            )
        ));
    }

    /**
     * Success test for check remote address and allow address. Here is enforced a few address ranges.
     *
     * @return void
     */
    public function testCheckAccess() {
        $this->_server->set('REMOTE_ADDR', '10.64.1.1');
        $prio = $this->_instance->checkAccess();
        $this->assertEquals(17, $prio);

        $this->_server->set('REMOTE_ADDR', '10.64.127.1');
        $prio = $this->_instance->checkAccess();
        $this->assertEquals(17, $prio);

        $this->_server->set('REMOTE_ADDR', '172.16.192.8');
        $prio = $this->_instance->checkAccess();
        $this->assertEquals(16, $prio);

        $this->_server->set('REMOTE_ADDR', '192.168.0.1');
        $prio = $this->_instance->checkAccess();
        $this->assertEquals(24, $prio);
    }

    /**
     * Fail test for check remote address and allow address. It fails because address is not in allowed address ranges.
     *
     * @return void
     */
    public function testCheckAccessFail() {
        $this->_server->set('REMOTE_ADDR', '10.64.128.1');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }

    /**
     * Fail test for check remote address and allow address. It fails because address is not in allowed address ranges.
     *
     * @return void
     */
    public function testCheckAccessFail2() {
        $this->_server->set('REMOTE_ADDR', '10.97.128.1');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }

    /**
     * Fail test for check remote address and allow address. It fails because address is not in allowed address ranges.
     *
     * @return void
     */
    public function testCheckAccessFail3() {
        $this->_server->set('REMOTE_ADDR', '172.15.127.8');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }
}
