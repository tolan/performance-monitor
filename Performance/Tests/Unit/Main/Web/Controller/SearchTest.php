<?php

namespace PM\Tests\Unit\Main\Web\Controller;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Search\Enum\Target;
use PM\Search\Enum\Filter;
use PM\Search\Enum\Type;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Controller\Search.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class SearchTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\Controller\Search
     */
    private $_controller;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_controller = $this->getProvider()->get('PM\Main\Web\Controller\Search');
    }

    /**
     * Success test for action filterMenu.
     *
     * @return void
     */
    public function testFilterMenu() {
        $payload  = $this->_controller->setAction('filterMenu')->run()->getData();

        $this->assertInternalType('array', $payload);
        $this->assertCount(3, $payload);

        $targets = array_values(Target::getConstants());
        $this->assertEquals($targets, array_keys($payload));
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureFulltext() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::FULLTEXT
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $expected = array(
            'type'   => Type::QUERY,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureName() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::NAME
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::STRING,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureEdited() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::EDITED
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::DATE,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureMethod() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::METHOD
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertArrayHasKey('values', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);
        unset($payload['values']);

        $expected = array(
            'type'   => Type::ENUM,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureUrl() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::URL
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::STRING,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureStarted() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::STARTED
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::DATE,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureTime() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::TIME
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(2, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::FLOAT,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsMeasureCalls() {
        $params = array(
            'target' => Target::MEASURE,
            'filter' => Filter::CALLS
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(4, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::INT,
            'name'   => 'search.filter.measure.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsTestUrl() {
        $params = array(
            'target' => Target::TEST,
            'filter' => Filter::URL
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::STRING,
            'name'   => 'search.filter.test.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getFilterParams.
     *
     * @return void
     */
    public function testGetFilterParamsAttemptUrl() {
        $params = array(
            'target' => Target::ATTEMPT,
            'filter' => Filter::URL
        );

        $payload  = $this->_controller->setParams($params)->setAction('getFilterParams')->run()->getData();
        $this->assertArrayHasKey('operators', $payload);
        $this->assertCount(3, $payload['operators']);
        unset($payload['operators']);

        $expected = array(
            'type'   => Type::STRING,
            'name'   => 'search.filter.attempt.'.$params['filter'],
            'target' => $params['target'],
            'filter' => $params['filter']
        );

        $this->assertInternalType('array', $expected);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action find.
     *
     * @return void
     */
    public function testFindAttempt() {
        $params = array(
            'filters' => array($this->_createFilter(Filter::FULLTEXT, null, Target::ATTEMPT, Type::QUERY, null)),
            'target' => Target::ATTEMPT
        );

        $response = $this->getMock('PM\Main\Web\Component\Request', array('getInput'));
        $response->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($params));
        $this->getProvider()->set($response, 'PM\Main\Web\Component\Request');

        $payload  = $this->_controller->setParams($params)->setAction('find')->run()->getData();
        $expected = array(
            'target' => Target::ATTEMPT,
            'result' => array(
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
                    'calls'            => '50',
                    'file'             => 'filetest.php'
                )
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('target', $payload);
        $this->assertEquals(Target::ATTEMPT, $payload['target']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action find.
     *
     * @return void
     */
    public function testFindMeasure() {
        $params = array(
            'filters' => array($this->_createFilter(Filter::FULLTEXT, null, Target::MEASURE, Type::QUERY, null)),
            'target' => Target::MEASURE
        );

        $response = $this->getMock('PM\Main\Web\Component\Request', array('getInput'));
        $response->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($params));
        $this->getProvider()->set($response, 'PM\Main\Web\Component\Request');

        $payload  = $this->_controller->setParams($params)->setAction('find')->run()->getData();
        $expected = array(
            'target' => Target::MEASURE,
            'result' => array(
                array(
                    'id'          => '2',
                    'name'        => 'second test',
                    'description' => NULL,
                    'edited'      => NULL,
                    'method'      => 'GET',
                    'url'         => 'perf.lc',
                    'started'     => '2013-12-21 13:37:49',
                    'calls'       => '50'
                )
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('target', $payload);
        $this->assertEquals(Target::MEASURE, $payload['target']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action find.
     *
     * @return void
     */
    public function testFindTest() {
        $params = array(
            'filters' => array($this->_createFilter(Filter::FULLTEXT, null, Target::TEST, Type::QUERY, null)),
            'target' => Target::TEST
        );

        $response = $this->getMock('PM\Main\Web\Component\Request', array('getInput'));
        $response->expects($this->once())
            ->method('getInput')
            ->will($this->returnValue($params));
        $this->getProvider()->set($response, 'PM\Main\Web\Component\Request');

        $payload  = $this->_controller->setParams($params)->setAction('find')->run()->getData();
        $expected = array(
            'target' => Target::TEST,
            'result' => array(
                array(
                    'id'        => '1',
                    'measureId' => '2',
                    'state'     => 'statistic_generated',
                    'started'   => '2013-12-21 13:37:49',
                    'url'       => 'perf.lc',
                    'time'      => '1376.51',
                    'calls'     => '50'
                )
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('target', $payload);
        $this->assertEquals(Target::TEST, $payload['target']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Helper method for create filter structure.
     *
     * @param string $filter   Filter type
     * @param string $operator Defined operator (optionaly)
     * @param string $target   Target of filter
     * @param string $type     Type of filter
     * @param string $value    Searched value
     *
     * @return array
     */
    private function _createFilter($filter, $operator, $target, $type, $value) {
        return get_defined_vars();
    }

}
