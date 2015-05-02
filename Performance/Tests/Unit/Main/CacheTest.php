<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Main\Cache;

/**
 * This script defines class for php unit test case of class \PM\Main\Cache.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class CacheTest extends TestCase {

    /**
     * Access instance.
     *
     * @var Cache
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_instance = $this->getProvider()->get('PM\Main\Cache');
    }

    /**
     * Success test for set driver.
     *
     * @return void
     */
    public function testSetDriver() {
        $driver = $this->getProvider()->get('PM\Main\Cache\Simple');
        $result = $this->_instance->setDriver($driver);

        $this->assertSame($result, $this->_instance);
    }

    /**
     * Success test for load data.
     *
     * @return void
     */
    public function testLoad() {
        $driver = $this->getProvider()->get('PM\Main\Cache\Simple'); /* @var $driver \PM\Main\Cache\Simple */
        $driver->save('test', 'AAA');
        $driver->save('array', array(1));
        $this->_instance->setDriver($driver);

        $this->assertEquals('AAA', $this->_instance->load('test'));
        $this->assertEquals(array(1), $this->_instance->load('array'));

        $expected = array(
            'test'  => 'AAA',
            'array' => array(1)
        );
        $this->assertEquals($expected, $this->_instance->load());
    }

    /**
     * Success test for save data.
     *
     * @return void
     */
    public function testSave() {
        $driver = $this->getProvider()->get('PM\Main\Cache\Simple');
        $this->_instance->setDriver($driver);

        $result = $this->_instance->save('key1', 'val1')
            ->save('key2', array('val2'));

        $this->assertSame($result, $this->_instance);
        $this->assertEquals('val1', $this->_instance->load('key1'));
        $this->assertEquals(array('val2'), $this->_instance->load('key2'));
    }

    /**
     * Success test for has data.
     *
     * @return void
     */
    public function testHas() {
        $driver = $this->getProvider()->get('PM\Main\Cache\Simple');
        $this->_instance
            ->setDriver($driver)
            ->save('key1', 'val2');

        $this->assertTrue($this->_instance->has('key1'));
        $this->assertFalse($this->_instance->has('key2'));
    }

    /**
     * Success test for clean data.
     *
     * @return void
     */
    public function testClean() {
        $driver = $this->getProvider()->get('PM\Main\Cache\Simple');
        $this->_instance
            ->setDriver($driver)
            ->save('key1', 'val2')
            ->save('key2', 'val1');

        $this->assertCount(2, $this->_instance->load());
        $this->_instance->clean('key1');
        $this->assertCount(1, $this->_instance->load());
        $this->_instance->save('key3', 'val3');
        $this->assertCount(2, $this->_instance->load());
        $this->_instance->clean();
        $this->assertCount(0, $this->_instance->load());
    }

    /**
     * Success test for commit data.
     *
     * @return void
     */
    public function testCommit() {
        $result = $this->_instance->commit();

        $this->assertSame($result, $this->_instance);
    }
}
