<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Commander.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class CommanderTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PM\Main\Commander
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_instance = $this->getProvider()->get('PM\Main\Commander');
    }

    /**
     * Success test for method getExecutor.
     *
     * @return void
     */
    public function testGetExecutor() {
        $executorTest = $this->_instance->getExecutor('test');

        $this->assertInstanceOf('PM\Main\Commander\Executor', $executorTest);

        $executor = $this->_instance->getExecutor('executor');

        $this->assertInstanceOf('PM\Main\Commander\Executor', $executor);
    }

    /**
     * Fail test for method getExecutor. It fails because name must be string.
     *
     * @expectedException PM\Main\Exception
     *
     * @return void
     */
    public function testGetExecutorFail() {
        $this->_instance->getExecutor(array('asd'));
    }

    /**
     * Succes test for method destroy executor.
     *
     * @return void
     */
    public function testDestroyExecutor() {
        $executor = $this->_instance->getExecutor('myTest');
        $this->assertInstanceOf('PM\Main\Commander\Executor', $executor);

        $answer = $this->_instance->destroyExecutor('myTest');
        $this->assertInstanceOf('PM\Main\Commander', $answer);
    }

    /**
     * Failt test for method destroy executor. It fails because executor must exist.
     *
     * @expectedException PM\Main\Exception
     *
     * @return void
     */
    public function testDestroyExecutorFail() {
        $this->_instance->destroyExecutor('test');
    }

    /**
     * Succes test for method hasExecutor.
     *
     * @return void
     */
    public function testHasExecutor() {
        $this->assertFalse($this->_instance->hasExecutor('test'));
        $this->_instance->getExecutor('test');
        $this->assertTrue($this->_instance->hasExecutor('test'));
        $this->assertFalse($this->_instance->hasExecutor('test2'));
        $this->_instance->destroyExecutor('test');
        $this->assertFalse($this->_instance->hasExecutor('test'));
    }
}
