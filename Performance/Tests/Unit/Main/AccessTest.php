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

        $this->_instance = $this->getProvider()->get('PF\Main\Access');
        $this->_server   = $this->getProvider()->get('PF\Main\Web\Component\Request')->getServer();
        $this->_config   = $this->getProvider()->get('PF\Main\Config');
    }

    /**
     * Success test for check remote address and allow address. Here is access control disabled.
     *
     * @return void
     */
    public function testCheckAccess() {
        $this->_config->set('access', array());

        $this->_server->set('REMOTE_ADDR', '10.1.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '192.168.1.1');
        $this->_instance->checkAccess();
    }

    /**
     * Success test for check remote address and denied address. Here is excluded a few address ranges.
     *
     * @return void
     */
    public function testCheckAccess1() {
        $this->_config->set('access', array(
            Access\DeniedFrom::CONFIG_KEY => array(
                '10.64.0.0/16',
                '10.96.0.0/16',
                '172.16.128.0/18',
                '192.168.0.1/24'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.1.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.63.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.65.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '172.16.0.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '172.16.197.63');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '172.16.254.63');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '224.24.16.9');
        $this->_instance->checkAccess();
    }

    /**
     * Success test for check remote address and allow address. Here is enforced a few address ranges.
     *
     * @return void
     */
    public function testCheckAccess2() {
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.64.0.0/17',
                '10.96.0.0/16',
                '172.16.128.0/16',
                '192.168.0.1/24'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.64.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.64.127.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.96.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '172.16.0.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '172.16.160.63');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '172.16.254.63');
        $this->_instance->checkAccess();

    }

    /**
     * Fail test for check remote address and allow address. It fails because address is not in allowed address ranges.
     *
     * @return void
     */
    public function testCheckDenied1() {
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.64.0.0/17',
                '10.96.0.0/16',
                '172.16.128.0/18',
                '192.168.0.1/24'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.64.128.1');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }

    /**
     * Fail test for check remote address and allow address. It fails because address is not in allowed address ranges.
     *
     * @return void
     */
    public function testCheckDenied2() {
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.64.0.0/17',
                '10.96.0.0/16',
                '172.16.128.0/18',
                '192.168.0.1/24'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.64.128.1');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }

    /**
     * Fail test for check remote address and denied address. It fails because address is in denied address ranges.
     *
     * @return void
     */
    public function testCheckDenied3() {
        $this->_config->set('access', array(
            Access\DeniedFrom::CONFIG_KEY => array(
                '10.64.0.0/17',
                '10.96.0.0/16',
                '172.16.128.0/18',
                '192.168.0.1/24'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.64.127.1');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }

    /**
     * Success test for check remote address and allow and denied address. Here are enforced address ranges and excluded some sub-ranges.
     *
     * @return void
     */
    public function testCheckAccessComplex() {
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.1.0.0/16',
                '10.96.0.0/14'
            ),
            Access\DeniedFrom::CONFIG_KEY => array(
                '10.1.16.0/23',
                '10.1.128.0/17'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.1.1.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.1.15.255');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.1.18.1');
        $this->_instance->checkAccess();

        $this->_server->set('REMOTE_ADDR', '10.99.1.1');
        $this->_instance->checkAccess();
    }

    /**
     * Fail test for check remote address  and allow and denied address. It fails because address is in denied address ranges.
     *
     * @return void
     */
    public function testCheckDeniedComplex1() {
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.1.0.0/16',
                '10.96.0.0/14'
            ),
            Access\DeniedFrom::CONFIG_KEY => array(
                '10.1.16.0/23',
                '10.1.128.0/17'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.1.17.5');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }

    /**
     * Fail test for check remote address  and allow and denied address. It fails because address is in denied address ranges.
     *
     * @return void
     */
    public function testCheckDeniedComplex2() {
        $this->_config->set('access', array(
            Access\AllowFrom::CONFIG_KEY => array(
                '10.1.0.0/16',
                '10.96.0.0/14'
            ),
            Access\DeniedFrom::CONFIG_KEY => array(
                '10.1.16.0/23',
                '10.1.128.0/16'
            )
        ));

        $this->_server->set('REMOTE_ADDR', '10.1.132.6');
        $this->setExpectedException('\PF\Main\Access\Exception');
        $this->_instance->checkAccess();
    }
}
