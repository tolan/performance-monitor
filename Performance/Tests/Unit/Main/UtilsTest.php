<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Utils.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class UtilsTest extends TestCase {

    /**
     *
     * @var \PM\Main\Utils
     */
    private $_utils;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_utils = $this->getProvider()->get('PM\Main\Utils');
    }

    /**
     * Success test for method toCamelCase.
     *
     * @return void
     */
    public function testToCamelCase() {
        $this->assertEquals('myTest', $this->_utils->toCamelCase('my test'));
        $this->assertEquals('myTest', $this->_utils->toCamelCase('my_test'));
        $this->assertEquals('myTest', $this->_utils->toCamelCase('my-test'));

        $this->assertEquals('myTestWithMoreWords', $this->_utils->toCamelCase('my test With-More_Words'));
        $this->assertEquals('myTestWithMoreWords', $this->_utils->toCamelCase('myTest_With-more_Words'));
    }

    /**
     * Success test for method convertTimeToMySQLDateTime.
     *
     * @return void
     */
    public function testConvertTimeToMySQLDateTime() {
        $times = array(0, 111, 123489435, 6433155, 223348648684);

        foreach ($times as $time) {
            $dateTime = $this->_utils->convertTimeToMySQLDateTime($time);
            $this->assertRegExp('#^\d{4}-(0[1-9])|(1[0-2])-([0-3]\d)|(3[0-1]) ([0-1]\d)|(2[0-3]):[0-5]\d:[0-5]\d$#', $dateTime);
            $this->assertEquals($time, strtotime($dateTime));
        }
    }

    /**
     * Success test for method convertTimeFromMySQLDateTime.
     *
     * @return void
     */
    public function testConvertTimeFromMySQLDateTime() {
        $times = array(
            '1970-01-01 00:00:00' => 0,
            '2011-12-11 12:32:11' => 1323606731,
            '2038-01-20 00:00:00' => 2147558400,
            '2090-11-11 22:22:22' => 3814122142
        );

        foreach ($times as $value => $expected) {
            $result = $this->_utils->convertTimeFromMySQLDateTime($value, false);
            $this->assertEquals($expected, $result);

            $result = $this->_utils->convertTimeFromMySQLDateTime($value, true);
            $this->assertEquals($expected * 1000, $result);
        }
    }

    /**
     * Success test for method convertMemory.
     *
     * @return void
     */
    public function testConvertMemory() {
        $data = array(
            'the file has 13.145 MB or 11KB' => 'the file has 13783532 B or 11264B',
            '0'                              => '0',
            '0KB 0 MB'                       => '0B 0 B',
            '20 KB'                          => '20480 B',
            '11,232 GB'                      => '12060268167 B'
        );

        foreach ($data as $value => $expected) {
            $result = $this->_utils->convertMemory($value);
            $this->assertEquals($expected, $result);
        }
    }

    /**
     * Success test for method isAssociativeArray.
     *
     * @return void
     */
    public function testIsAssociativeArray() {
        $array = range(1,100);
        $this->assertFalse($this->_utils->isAssociativeArray($array));

        $array = array('1' => 11, 'a' => 'AA');
        $this->assertTrue($this->_utils->isAssociativeArray($array));

        $array = array(1 => 11, 2 => 'AA');
        $this->assertTrue($this->_utils->isAssociativeArray($array));

        $array = array();
        $this->assertFalse($this->_utils->isAssociativeArray($array));

        $array = array(1, 3, 5);
        $this->assertFalse($this->_utils->isAssociativeArray($array));
    }

    /**
     * Success test for method convertToBoolean.
     *
     * @return void
     */
    public function testConvertToBoolean() {
        $data = array(
            true    => true,
            false   => false,
            'true'  => true,
            'false' => false,
            'test'  => true,
            0       => false,
            '0'     => false,
            1       => true
        );

        foreach ($data as $value => $expected) {
            $this->assertSame($expected, $this->_utils->convertToBoolean($value));
        }
    }

    /**
     * Success test for method getShortName.
     *
     * @return void
     */
    public function testGetShortName() {
        $classes = array(
            'PM\Main\Database'            => 'Database',
            'PM\Main\Commander\Execution' => 'Execution',
            'PM\Main\Event\Action\Emit'   => 'Emit',
            'PM\Main\Event\Listener\On'   => 'On',
            'PM\Main\Event\Manager'       => 'Manager'
        );

        foreach ($classes as $value => $expected) {
            $this->assertSame($expected, $this->_utils->getShortName($value));
        }
    }
}
