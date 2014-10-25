<?php

namespace PM\Tests\Unit\Main\Web\Controller;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Controller\Profiler.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ProfilerTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\Controller\Profiler
     */
    private $_controller;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_controller = $this->getProvider()->get('PM\Main\Web\Controller\Profiler');
    }

    /**
     * Success test for action measures.
     *
     * @return void
     */
    public function testMeasures() {
        $payload  = $this->_controller->setAction('measures')->run()->getData();
        $expected = array(
            array(
                'id'          => '1',
                'name'        => 'first test',
                'description' => NULL,
                'edited'      => 0
            ),
            array(
                'id'          => '2',
                'name'        => 'second test',
                'description' => NULL,
                'edited'      => 0
            ),
            array (
                'id'          => '3',
                'name'        => 'third test',
                'description' => NULL,
                'edited'      => 0
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(3, $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action measure.
     *
     * @return void
     */
    public function testMeasure() {
        $payload  = $this->_controller->setParams(array('id' => 2))->setAction('measure')->run()->getData();
        $expected = array(
            'id'          => '2',
            'name'        => 'second test',
            'description' => NULL,
            'edited'      => NULL,
            'requests'    => array(
                array(
                    'id'         => '1',
                    'method'     => 'GET',
                    'url'        => 'perf.lc',
                    'toMeasure'  => true,
                    'parameters' => array()
                )
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertArrayHasKey('requests', $payload);
        $this->assertInternalType('array', $payload['requests']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action delete.
     *
     * @return void
     */
    public function testDelete() {
        $original = $this->_controller->setAction('measures')->run()->getData();
        $this->_controller->setParams(array('id' => 1))->setAction('delete')->run()->getData();
        $afterDelete = $this->_controller->setAction('measures')->run()->getData();

        $this->assertEquals(count($original) - 1, count($afterDelete));
        $this->assertEquals(2, $afterDelete[0]['id']);
    }

    /**
     * Success test for action create.
     *
     * @return void
     */
    public function testCreate() {
        $new = array(
            'name'        => 'second test',
            'description' => NULL
        );

        $response = $this->getMock('PM\Main\Web\Component\Request', array('getInput'));
        $response->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($new));

        $this->getProvider()->set($response, 'PM\Main\Web\Component\Request');

        $id      = $this->_controller->setAction('create')->run()->getData();
        $measure = $this->_controller->setParams(array('id' => $id))->setAction('measure')->run()->getData();
        $new['id'] = $id;
        unset($measure['edited']);

        $this->assertInternalType('array', $measure);
        $this->assertEquals($new, $measure);
    }

    /**
     * Success test for action update.
     *
     * @return void
     */
    public function testUpdate() {
        $update = array(
            'id'   => 2,
            'name' => 'new name'
        );

        $response = $this->getMock('PM\Main\Web\Component\Request', array('getInput'));
        $response->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($update));

        $this->getProvider()->set($response, 'PM\Main\Web\Component\Request');

        $updated = $this->_controller->setAction('update')->setParams($update)->run()->getData();
        $this->assertTrue($updated);

        $measure = $this->_controller->setParams($update)->setAction('measure')->run()->getData();
        $this->assertEquals($update['name'], $measure['name']);
    }

    /**
     * Success test for action tests.
     *
     * @return void
     */
    public function testTests() {
        $payload  = $this->_controller->setParams(array('measureId' => 2))->setAction('tests')->run()->getData();
        $expected = array(
            array(
                'id'        => '1',
                'measureId' => '2',
                'state'     => 'statistic_generated',
                'started'   => '2013-12-21 13:37:49'
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action test.
     *
     * @return void
     */
    public function testTest() {
        $payload  = $this->_controller->setParams(array('id' => 1))->setAction('test')->run()->getData();
        $expected = array(
            'id'        => '1',
            'measureId' => '2',
            'state'     => 'statistic_generated',
            'started'   => '2013-12-21 13:37:49'
        );

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action deleteTest.
     *
     * @return void
     */
    public function testDeleteTest() {
        $original = $this->_controller->setParams(array('measureId' => 2))->setAction('tests')->run()->getData();
        $this->_controller->setParams(array('id' => 1))->setAction('deleteTest')->run()->getData();
        $afterDelete = $this->_controller->setParams(array('measureId' => 2))->setAction('tests')->run()->getData();

        $this->assertEquals(count($original) - 1, count($afterDelete));
    }

    /**
     * Success test for action getAttempts.
     *
     * @return void
     */
    public function testGetAttempts() {
        $payload  = $this->_controller->setParams(array('testId' => 1))->setAction('getAttempts')->run()->getData();
        $expected = array(
            array(
                'id'               => '1',
                'testId'           => '1',
                'url'              => 'perf.lc',
                'method'           => 'GET',
                'parameters'       => NULL,
                'body'             => NULL,
                'state'            => 'statistic_generated',
                'started'          => '2013-12-21 13:37:49',
                'compensationTime' => '0.0626818',
                'time'             => '1376.51',
                'calls'            => '50'
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getAttemptStatistic.
     *
     * @return void
     */
    public function testGetAttemptStatistic() {
        $payload  = $this->_controller->setParams(array('id' => 1))->setAction('getAttemptStatistic')->run()->getData();
        $expected = array(
            'id'               => '1',
            'testId'           => '1',
            'url'              => 'perf.lc',
            'method'           => 'GET',
            'parameters'       => NULL,
            'body'             => NULL,
            'state'            => 'statistic_generated',
            'started'          => '1387629469000',
            'compensationTime' => '0.0626818',
            'time'             => '1376.51',
            'calls'            => '50',
            'maxImmersion'     => '4'
        );

        $this->assertInternalType('array', $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getAttemptCallStack.
     *
     * @return void
     */
    public function testGetAttemptCallStack() {
        $payload  = $this->_controller->setParams(array('id' => 1, 'parentId' => 0))->setAction('getAttemptCallStack')->run()->getData();
        $expected = array(
            array (
                'id'           => '1',
                'attemptId'    => '1',
                'parentId'     => '0',
                'file'         => 'filetest.php',
                'line'         => '10',
                'content'      => 'sleep(1);',
                'time'         => '1000.24',
                'timeSubStack' => '0'
            ),
            array (
                'id'           => '2',
                'attemptId'    => '1',
                'parentId'     => '0',
                'file'         => 'filetest.php',
                'line'         => '12',
                'content'      => 'echo \'aaa\';',
                'time'         => '0.124',
                'timeSubStack' => '0'
            ),
            array (
                'id'           => '3',
                'attemptId'    => '1',
                'parentId'     => '0',
                'file'         => 'filetest.php',
                'line'         => '13',
                'content'      => 'die();',
                'time'         => '1.8',
                'timeSubStack' => '0'
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(3, $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getAttemptFunctionStatistic.
     *
     * @return void
     */
    public function testGetAttemptFunctionStatistic() {
        $payload  = $this->_controller->setParams(array('id' => 1))->setAction('getAttemptFunctionStatistic')->run()->getData();
        $expected = array(
            array (
                'id'              => 1,
                'file'            => 'filetest.php',
                'line'            => 10,
                'content'         => 'sleep(1);',
                'time'            => 1000.2399902344,
                'avgTime'         => 1000.2399902344,
                'timeSubStack'    => 0,
                'avgTimeSubStack' => 0,
                'count'           => 1,
                'min'             => 1000.24,
                'max'             => 1000.24
            ),
            array (
                'id'              => 2,
                'file'            => 'filetest.php',
                'line'            => 12,
                'content'         => 'echo \'aaa\';',
                'time'            => 0.12399999797344,
                'avgTime'         => 0.12399999797344,
                'timeSubStack'    => 0,
                'avgTimeSubStack' => 0,
                'count'           => 1,
                'min'             => 0.124,
                'max'             => 0.124,
            ),
            array (
                'id'              => 3,
                'file'            => 'filetest.php',
                'line'            => 13,
                'content'         => 'die();',
                'time'            => 1.7999999523163,
                'avgTime'         => 1.7999999523163,
                'timeSubStack'    => 0,
                'avgTimeSubStack' => 0,
                'count'           => 1,
                'min'             => 1.8,
                'max'             => 1.8,
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(3, $payload);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getMethods.
     *
     * @return void
     */
    public function testGetMethods() {
        $payload  = $this->_controller->setAction('getMethods')->run()->getData();

        $this->assertInternalType('array', $payload);
        $this->assertArrayHasKey('requests', $payload);
        $this->assertArrayHasKey('params', $payload);

        $paramKeys = \PM\Main\Http\Enum\Method::getConstants();

        $this->assertEquals(sort(array_values($paramKeys)), sort(array_keys($payload['params'])));
    }
}
