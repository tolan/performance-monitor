<?php

namespace PF\Tests\Unit\Main\Event\Action;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Event\Action\Emit.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class EmitTest extends TestCase {

    /**
     * action instance.
     *
     * @var \PF\Main\Event\Action\Emit
     */
    private $_action;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_action = $this->getProvider()->prototype('PF\Main\Event\Action\Emit');

        parent::setUp();
    }

    /**
     * Success test for get data.
     *
     * @return void
     */
    public function testGetData() {
        $action = $this->_action;
        $this->assertEquals(null, $action->getData());

        $action->setData(array());
        $this->assertEquals(array(), $action->getData());
    }

    /**
     * Success test for set data.
     *
     * @return void
     */
    public function testSetData() {
        $this->assertInstanceOf('PF\Main\Event\Action\Emit', $this->_action->setData(array()));
        $this->assertEquals(array(), $this->_action->getData());
    }

    /**
     * Success test for get module.
     *
     * @return void
     */
    public function testGetModule() {
        $action = $this->_action;

        $this->assertEquals(null, $action->getModule());

        $action->setModule('Test');
        $this->assertEquals('Test', $action->getModule());
    }

    /**
     * Success test for set module.
     *
     * @return void
     */
    public function testSetModule() {
        $this->assertInstanceOf('PF\Main\Event\Action\Emit', $this->_action->setModule('Module'));
        $this->assertEquals('Module', $this->_action->getModule());
    }

    /**
     * Success test for get name.
     *
     * @return void
     */
    public function testGetName() {
        $action = $this->_action->setName('name');
        $this->assertEquals('name', $action->getName());
    }

    /**
     * Fail test for get name.
     *
     * @expectedException PF\Main\Event\Exception
     *
     * @return void
     */
    public function testGetNameFail() {
        $this->_action->getName();
    }

    /**
     * Success test for set name.
     *
     * @return void
     */
    public function testSetName() {
        $this->assertInstanceOf('PF\Main\Event\Action\Emit', $this->_action->setName('test'));
        $this->assertEquals('test', $this->_action->getName());
    }
}
