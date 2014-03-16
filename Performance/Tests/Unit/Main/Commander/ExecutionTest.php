<?php

namespace PF\Tests\Unit\Main\Commander;

use PF\Main\Abstracts\Unit\TestCase;
use PF\Main\Commander\Result;
use PF\Main\Commander\Execution;

/**
 * This script defines class for php unit test case of class \PF\Main\Commander\Execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ExecutionTest  extends TestCase {

    /**
     * Success test for method execute with closure function.
     *
     * @return void
     */
    public function testClosure() {
        $execution = new Execution(function(Result $entity, $data, $const = 100) {
            $entity->setResult(5);
            return array('data' => $const);
        });

        $result = new Result();
        $execution->execute($result, $this->getProvider());
        $expected = array(
            'result' => 5,
            'data'   => 100
        );

        $this->assertInstanceOf('PF\Main\Commander\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }

    /**
     * Success test for method execute with defined scope (class) and method.
     *
     * @return void
     */
    public function testClass() {
        $class = new TestingClass();
        $execution = new Execution('test', $class);

        $result = new Result();
        $execution->execute($result, $this->getProvider());
        $expected = array(
            'data'  => 200,
            'const' => 100
        );

        $this->assertInstanceOf('PF\Main\Commander\Result', $result);
        $this->assertEquals($expected, $result->toArray());
    }
}
