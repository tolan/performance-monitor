<?php

namespace PF\Tests\Unit\Main;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Utils.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class UtilsTest extends TestCase {

    /**
     *
     * @var \PF\Main\Utils
     */
    private $_utils;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_utils = $this->getProvider()->get('PF\Main\Utils');
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
}
