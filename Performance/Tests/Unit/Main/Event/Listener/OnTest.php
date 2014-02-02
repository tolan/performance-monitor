<?php

namespace PF\Tests\Unit\Main\Event\Listener;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Event\Listener\On.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class OnTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Event\Listener\On
     */
    private $_listener;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_listener = $this->getProvider()->prototype('PF\Main\Event\Listener\On');

        parent::setUp();
    }

    /**
     * Success test for get closure.
     *
     * @return void
     */
    public function testGetClosure() {
        $listener = $this->_listener;
        $closure  = function () {};

        $listener->setClosure($closure);
        $this->assertEquals($closure, $listener->getClosure());
    }

    /**
     * Fail test for get closure.
     *
     * @expectedException \PF\Main\Event\Exception
     *
     * @return void
     */
    public function testGetClosureFail() {
        $this->_listener->getClosure();
    }

    /**
     * Success test for set closure.
     *
     * @return void
     */
    public function testSetClosure() {
        $closure  = function () {};
        $this->assertInstanceOf('PF\Main\Event\Listener\On', $this->_listener->setClosure($closure));
        $this->assertEquals($closure, $this->_listener->getClosure());
    }

    /**
     * Success test for get module.
     *
     * @return void
     */
    public function testGetModule() {
        $listener = $this->_listener;

        $this->assertEquals(null, $listener->getModule());

        $listener->setModule('Test');
        $this->assertEquals('Test', $listener->getModule());
    }

    /**
     * Success test for set module.
     *
     * @return void
     */
    public function testSetModule() {
        $this->assertInstanceOf('PF\Main\Event\Listener\On', $this->_listener->setModule('Module'));
        $this->assertEquals('Module', $this->_listener->getModule());
    }

    /**
     * Success test for get name.
     *
     * @return void
     */
    public function testGetName() {
        $listener = $this->_listener->setName('name');
        $this->assertEquals('name', $listener->getName());
    }

    /**
     * Fail test for get name.
     *
     * @expectedException PF\Main\Event\Exception
     *
     * @return void
     */
    public function testGetNameFail() {
        $this->_listener->getName();
    }

    /**
     * Success test for set name.
     *
     * @return void
     */
    public function testSetName() {
        $this->assertInstanceOf('PF\Main\Event\Listener\On', $this->_listener->setName('test'));
        $this->assertEquals('test', $this->_listener->getName());
    }
}
