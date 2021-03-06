<?php

namespace PM\Tests\Unit\Main\Event\Listener;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Event\Listener\Once.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class OnceTest extends TestCase {

    /**
     * Listener instance.
     *
     * @var \PM\Main\Event\Listener\Once
     */
    private $_listener;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_listener = $this->getProvider()->prototype('PM\Main\Event\Listener\Once');

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
     * @expectedException \PM\Main\Event\Exception
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
        $this->assertInstanceOf('PM\Main\Event\Listener\Once', $this->_listener->setClosure($closure));
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
        $this->assertInstanceOf('PM\Main\Event\Listener\Once', $this->_listener->setModule('Module'));
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
     * @expectedException PM\Main\Event\Exception
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
        $this->assertInstanceOf('PM\Main\Event\Listener\Once', $this->_listener->setName('test'));
        $this->assertEquals('test', $this->_listener->getName());
    }
}
