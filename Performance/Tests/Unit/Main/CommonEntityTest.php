<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Main\CommonEntity;

/**
 * This script defines class for php unit test case of class \PM\Main\CommonEntity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class CommonEntityTest extends TestCase {

    /**
     * Access instance.
     *
     * @var CommonEntity
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_instance = new CommonEntity();
    }

    /**
     * Success test for construct method.
     *
     * @return void
     */
    public function testConstruct() {
        $data     = array('key1' => 'val1');
        $instance = new CommonEntity($data);

        $this->assertInstanceOf('PM\Main\CommonEntity', $instance);
        $this->assertInternalType('array', $instance->toArray());
        $this->assertArrayHasKey('key1', $instance->toArray());
        $this->assertEquals($data, $instance->toArray());
    }

    /**
     * Success test for method fromArray with string.
     *
     * @return void
     */
    public function testFromArrayString() {
        $data     = 'string';
        $instance = $this->_instance->fromArray($data);

        $this->assertInstanceOf('PM\Main\CommonEntity', $instance);
        $this->assertInternalType('array', $instance->toArray());
        $this->assertEquals((array)$data, $instance->toArray());
    }

    /**
     * Success test for method fromArray with simple array.
     *
     * @return void
     */
    public function testFromArraySimpleArray() {
        $data     = array('from' => 'array');
        $instance = $this->_instance->fromArray($data);

        $this->assertInstanceOf('PM\Main\CommonEntity', $instance);
        $this->assertInternalType('array', $instance->toArray());
        $this->assertArrayHasKey('from', $instance->toArray());
        $this->assertEquals($data, $instance->toArray());
    }

    /**
     * Success test for method fromArray with instance.
     *
     * @return void
     */
    public function testFromArrayInstance() {
        $instanceB = new CommonEntity(array('from2' => 'aaa'));
        $instance = $this->_instance->fromArray($instanceB);

        $this->assertInstanceOf('PM\Main\CommonEntity', $instance);
        $this->assertInternalType('array', $instance->toArray());
        $this->assertArrayHasKey('from2', $instance->toArray());
        $this->assertEquals($instanceB->toArray(), $instance->toArray());
    }

    /**
     * Success test for method getVersion.
     *
     * @return void
     */
    public function testGetVersion() {
        $this->assertEquals(0, $this->_instance->getVersion());

        $this->_instance->set('key1', 'val1');
        $this->assertEquals(1, $this->_instance->getVersion());

        $this->_instance->fromArray(
            array(
                'key2' => 'val2',
                'key3' => 'val3'
            )
        );
        $this->assertEquals(3, $this->_instance->getVersion());

        $this->_instance->reset();
        $this->assertEquals(0, $this->_instance->getVersion());
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGetSuccess() {
        $data = array(
            'key1' => array('test')
        );
        $this->_instance->fromArray($data);

        $this->assertArrayHasKey('key1', $this->_instance->toArray());
        $this->assertEquals($data['key1'], $this->_instance->get('key1'));
    }

    /**
     * Error test for method get. The property test was not set.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testGetError() {
        $this->_instance->get('test');
    }

    /**
     * Success test for method set.
     *
     * @return void
     */
    public function testSet() {
        $this->assertEmpty($this->_instance->toArray());

        $instance = $this->_instance->set('key1', 'val1');
        $this->assertInstanceOf('PM\Main\CommonEntity', $instance);
        $this->assertSame($this->_instance, $instance);
        $this->assertArrayHasKey('key1', $instance->toArray());
        $this->assertCount(1, $instance->toArray());
        $this->assertEquals(1, $instance->getVersion());
        $this->assertEquals('val1', $this->_instance->get('key1'));
    }

    /**
     * Success test for method reset with undefined name.
     *
     * @return void
     */
    public function testResetNullSuccess() {
        $this->assertEmpty($this->_instance->toArray());
        $this->_instance->reset();
        $this->assertEmpty($this->_instance->toArray());
        $this->assertEquals(0, $this->_instance->getVersion());

        $this->_instance->set('key1', 'val');
        $this->_instance->set('key2', 'val');
        $this->assertNotEmpty($this->_instance->toArray());
        $this->assertEquals(2, $this->_instance->getVersion());

        $this->_instance->reset();
        $this->assertEmpty($this->_instance->toArray());
        $this->assertEquals(0, $this->_instance->getVersion());
    }

    /**
     * Success test for method reset with defined name.
     *
     * @return void
     */
    public function testResetSuccess() {
        $this->_instance->set('key1', 'val');
        $this->_instance->set('key2', 'val');
        $this->assertArrayHasKey('key1', $this->_instance->toArray());
        $this->assertEquals(2, $this->_instance->getVersion());

        $this->_instance->reset('key1');
        $this->assertArrayNotHasKey('key1', $this->_instance->toArray());
        $this->assertEquals(3, $this->_instance->getVersion());

        $this->_instance->reset('_version');
        $this->assertEquals(0, $this->_instance->getVersion());
    }

    /**
     * Error test for method reset with defined name.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testResetError() {
        $this->assertEmpty($this->_instance->toArray());

        $this->_instance->reset('key');
    }

    /**
     * Success test for method has.
     *
     * @return void
     */
    public function testHas() {
        $this->assertEmpty($this->_instance->toArray());
        $this->assertFalse($this->_instance->has('key'));

        $this->_instance->set('key', 'val');
        $this->assertTrue($this->_instance->has('key'));

        $this->_instance->reset('key');
        $this->assertFalse($this->_instance->has('key'));
    }

    /**
     * Success test for method isEmpty.
     *
     * @return void
     */
    public function testIsEmptySuccess() {
        $this->_instance->set('key', null);

        $this->assertTrue($this->_instance->has('key'));
        $this->assertTrue($this->_instance->isEmpty('key'));

        $this->_instance->set('key2', array());

        $this->assertTrue($this->_instance->has('key2'));
        $this->assertTrue($this->_instance->isEmpty('key2'));

        $this->_instance->set('key3', array('val'));

        $this->assertTrue($this->_instance->has('key3'));
        $this->assertFalse($this->_instance->isEmpty('key3'));
    }

    /**
     * Error test for method isEmpty. Property key was not set.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testIsEmptyError() {
        $this->assertEmpty($this->_instance->toArray());

        $this->_instance->isEmpty('key');
    }

    /**
     * Success test for method get via direct access.
     *
     * @return void
     */
    public function testAccessGetSuccess() {
        $this->assertEmpty($this->_instance->toArray());

        $this->_instance->set('key', 'val');
        $this->assertTrue($this->_instance->has('key'));
        $this->assertEquals('val', $this->_instance->key);
    }

    /**
     * Error test for method get via direct access. Property test was not set.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testAccessGetError() {
        $this->_instance->test;
    }

    /**
     * Success test for method set via direct access.
     *
     * @return void
     */
    public function testAccessSet() {
        $this->assertFalse($this->_instance->has('test'));

        $this->_instance->test = 'val';

        $this->assertTrue($this->_instance->has('test'));
        $this->assertEquals('val', $this->_instance->test);
    }

    /**
     * Success test for method get via magic method _call.
     *
     * @return void
     */
    public function testMagicGetSuccess() {
        $this->assertEmpty($this->_instance->toArray());

        $this->_instance->set('key', 'val');
        $this->assertTrue($this->_instance->has('key'));
        $this->assertEquals('val', $this->_instance->getKey());
    }

    /**
     * Success test for method get via magic method _call. Property test was not set.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testMagicGetError() {
        $this->_instance->getTest();
    }

    /**
     * Success test for method set via magic method _call.
     *
     * @return void
     */
    public function testMagisSet() {
        $this->assertFalse($this->_instance->has('test'));

        $instance = $this->_instance->setTest('val');

        $this->assertInstanceOf('PM\Main\CommonEntity', $instance);
        $this->assertTrue($this->_instance->has('test'));
        $this->assertEquals('val', $this->_instance->test);
    }

    /**
     * Success test for method has via magic method _call.
     *
     * @return void
     */
    public function testMagicHas() {
        $this->assertEmpty($this->_instance->toArray());
        $this->assertFalse($this->_instance->hasKey());

        $this->_instance->set('key', 'val');
        $this->assertTrue($this->_instance->hasKey());

        $this->_instance->reset('key');
        $this->assertFalse($this->_instance->hasKey());
    }

    /**
     * Success test for method has via direct access.
     *
     * @return void
     */
    public function testAccessHas() {
        $this->assertEmpty($this->_instance->toArray());
        $this->assertFalse(isset($this->_instance->key));

        $this->_instance->set('key', 'val');
        $this->assertTrue(isset($this->_instance->key));

        $this->_instance->reset('key');
        $this->assertFalse(isset($this->_instance->key));
    }

    /**
     * Success test for method reset via direct access.
     *
     * @return void
     */
    public function testAccessResetSuccess() {
        $this->_instance->set('key1', 'val');
        $this->_instance->set('key2', 'val');
        $this->assertArrayHasKey('key1', $this->_instance->toArray());
        $this->assertEquals(2, $this->_instance->getVersion());

        unset($this->_instance->key1);
        $this->assertArrayNotHasKey('key1', $this->_instance->toArray());
        $this->assertEquals(3, $this->_instance->getVersion());

        unset($this->_instance->_version);
        $this->assertEquals(0, $this->_instance->getVersion());
    }

    /**
     * Error test for method reset via direct access.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testAccessResetError() {
        $this->assertEmpty($this->_instance->toArray());

        unset($this->_instance->key);
    }

    /**
     * Error test for undefined method. It resolves method _call and throws exception.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testUndefinedMethod() {
        $this->_instance->test();
    }

}
