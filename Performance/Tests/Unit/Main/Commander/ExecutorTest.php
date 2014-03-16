<?php

namespace PF\Tests\Unit\Main\Commander;

use PF\Main\Abstracts\Unit\TestCase;
use PF\Main\Commander\Result;

/**
 * This script defines class for php unit test case of class \PF\Main\Commander\Executor.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ExecutorTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Commander\Executor
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_instance = $this->getProvider()->prototype('PF\Main\Commander\Executor', true);
    }

    /**
     * Success test for method clean.
     *
     * @return void
     */
    public function testClean() {
        $instance = $this->_instance;
        $instance->add('test');
        $instance->add('test2');

        $this->assertCount(2, $instance->get());
        $this->_instance->clean();
        $this->assertCount(0, $instance->get());
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGet() {
        $instance = $this->_instance;
        $instance->add('test');
        $instance->add('test2');

        $this->assertInternalType('array', $instance->get());
        foreach ($instance->get() as $command) {
            $this->assertInstanceOf('PF\Main\Commander\Execution', $command);
        }

        $this->assertCount(2, $instance->get());
    }

    /**
     * Success test for method getResult.
     *
     * @return void
     */
    public function testGetResult() {
        $result = $this->_instance->getResult();

        $this->assertInstanceOf('PF\Main\Commander\Result', $result);
    }

    /**
     * Success test for method setResult.
     *
     * @return void
     */
    public function testSetResult() {
        $myResult = new Result();
        $myResult->setTest(true);

        $answer = $this->_instance->setResult($myResult);

        $this->assertInstanceOf('PF\Main\Commander\Executor', $answer);
        $this->assertInstanceOf('PF\Main\Commander\Result', $answer->getResult());
        $this->assertTrue($answer->getResult()->getTest());
    }

    /**
     * Success test for method execute with closure.
     *
     * @return void
     */
    public function testExecuteClosure() {
        $this->_instance->add(function(Result $entity, $data, $const = 100) {
            $entity->setResult(5);
            return array('data' => $const);
        })->add(function($data) {
            return array('processed' => $data * 10);
        })->execute();

        $result = $this->_instance->getResult();
        $expected = array(
            'result' => 5,
            'data' => 100,
            'processed' => 1000
        );

        $this->assertInstanceOf('PF\Main\Commander\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Success test for method execute with defined scope (class) and method.
     *
     * @return void
     */
    public function testExecuteClass() {
        $class = new TestingClass();

        $this->_instance->add('test', $class)
            ->add('test', $class)
            ->execute();

        $result = $this->_instance->getResult();
        $expected = array(
            'data'  => 20000,
            'const' => 100
        );

        $this->assertInstanceOf('PF\Main\Commander\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }
}

