<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Main\Config;

/**
 * This script defines class for php unit test case of class \PM\Main\Config.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ConfigTest extends TestCase {

    /**
     *
     * @var \PM\Main\Config
     */
    private $_config;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_config = $this->getProvider()->prototype('PM\Main\Config');
    }

    /**
     * Success test for method toArray.
     *
     * @return void
     */
    public function testToArray() {
        $this->_config->reset();
        $expected = array(
        );

        $config = $this->_config->toArray();
        $this->assertInternalType('array', $config);
        $this->assertEquals($expected, $config);

        $config = $this->_config->set('test', array('data'))->toArray();
        $expected['test'] = array('data');
        $this->assertInternalType('array', $config);
        $this->assertEquals($expected, $config);
    }

    /**
     * Success test for method fromArray.
     *
     * @return void
     */
    public function testFromArray() {
        $this->_config->reset();
        $expected = array(
            'namespace' => 'test',
            'test'      => 'text'
        );

        $config = $this->_config->fromArray($expected)->toArray();
        $this->assertInternalType('array', $config);
        $this->assertEquals($expected, $config);
    }

    /**
     * Success test for method loadJson.
     *
     * @return void
     */
    public function testLoadJson() {
        $this->_config->reset();
        $expected = array(
            'name' => 'test'
        );

        $config = $this->_config->loadJson(__DIR__.'/ConfigTest.json')->toArray();
        $this->assertInternalType('array', $config);
        $this->assertEquals($expected, $config);
    }

    /**
     * Success test for method hasOwnProperty.
     *
     * @return void
     */
    public function testHasOwnProperty() {
        $this->assertTrue($this->_config->hasOwnProperty('root'));
        $this->assertFalse($this->_config->hasOwnProperty('test'));
    }

    /**
     * Success test for method getter.
     *
     * @return void
     */
    public function testGetter() {
        $this->_config->set('test', 'data');

        $this->assertEquals('data', $this->_config->get('test'));
        $this->assertEquals('data', $this->_config->getTest());
    }

    /**
     * Success test for method setter.
     *
     * @return void
     */
    public function testSetter() {
        $this->_config->set('test1', 'data1');

        $this->assertEquals('data1', $this->_config->get('test1'));

        $this->_config->setTest2('data2');

        $this->assertEquals('data2', $this->_config->get('test2'));
    }

    /**
     * Success test for method reset.
     *
     * @return void
     */
    public function testReset() {
        $data = array(
            'test1' => 'data1',
            'test2' => 'data2',
            'test3' => 'data3'
        );

        $this->_config->fromArray($data);

        $this->assertTrue($this->_config->hasOwnProperty('test3'));
        $this->assertFalse($this->_config->reset('test3')->hasOwnProperty('test3'));
        $this->assertTrue($this->_config->hasOwnProperty('test2'));

        $this->assertEmpty($this->_config->reset()->toArray());
    }
}
