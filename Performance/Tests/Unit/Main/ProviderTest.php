<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Main\Provider;

/**
 * This script defines class for php unit test case of class \PM\Main\Provider.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ProviderTest extends TestCase {

    /**
     * Provider
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_provider = $this->getProvider()->get('PM\Main\Provider');
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGetSimple() {
        $instance = $this->_provider->get('PM\Tests\Unit\Main\Foo');

        $this->assertInstanceOf('PM\Tests\Unit\Main\Foo', $instance);
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGetWithDependecies() {
        $instance = $this->_provider->get('PM\Tests\Unit\Main\FooDep');

        $this->assertInstanceOf('PM\Tests\Unit\Main\FooDep', $instance);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Foo', $instance->foo);
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGetWithAlias() {
        $config  = $this->_provider->get('config')->get('provider');
        $aliases = $config['serviceMap'];

        foreach ($aliases as $alias => $className) {
            $instance = $this->_provider->get($alias);
            $this->assertInstanceOf($className, $instance);
        }
    }

    /**
     * Fail test for method get.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testGetCycleSimple() {
        $this->_provider->get('PM\Tests\Unit\Main\CycleSelf');
    }

    /**
     * Fail test for method get.
     *
     * @expectedException \PM\Main\Exception
     *
     * @return void
     */
    public function testGetCycleInherit() {
        $this->_provider->get('PM\Tests\Unit\Main\CycleInherit');
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGetDeep10() {
        $deep2 = $this->_provider->get('PM\Tests\Unit\Main\Deep2');
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep2', $deep2);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep1', $deep2->deep);

        $deep4 = $this->_provider->get('PM\Tests\Unit\Main\Deep4');
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep4', $deep4);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep3', $deep4->deep);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep2', $deep4->deep->deep);

        $deep10 = $this->_provider->get('PM\Tests\Unit\Main\Deep10');
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep10', $deep10);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep8', $deep10->deep->deep);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep4', $deep10->deep->deep->deep->deep->deep->deep);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep1', $deep10->deep->deep->deep->deep->deep->deep->deep->deep->deep);
    }

    /**
     * Success test for method get.
     *
     * @return void
     */
    public function testGetDeepMulti() {
        $deepMulti = $this->_provider->get('PM\Tests\Unit\Main\DeepMulti');
        $this->assertInstanceOf('PM\Tests\Unit\Main\DeepMulti', $deepMulti);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep3', $deepMulti->deep3);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep1', $deepMulti->deep3->deep->deep);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep8', $deepMulti->deep8);
        $this->assertInstanceOf('PM\Tests\Unit\Main\Deep5', $deepMulti->deep8->deep->deep->deep);
    }

    /**
     * Success test for method getInstance.
     *
     * @return void
     */
    public function testGetInstance() {
        $instance = Provider::getInstance();

        $this->assertInstanceOf('PM\Main\Provider', $instance);
    }

    /**
     * Success test for method getListInstances.
     *
     * @return void
     */
    public function testGetListinstances() {
        $originalList = $this->_provider->getListInstances();
        $this->assertInternalType('array', $originalList);

        $this->_provider->get('PM\Tests\Unit\Main\Deep2');
        $list = $this->_provider->getListInstances();
        $this->assertEquals(2, (count($list) - count($originalList)));

        $this->_provider->get('PM\Tests\Unit\Main\Deep6');
        $list = $this->_provider->getListInstances();
        $this->assertEquals(6, (count($list) - count($originalList)));

        $expected = array(
            'name'      => 'PM\Tests\Unit\Main\Deep6',
            'classname' => 'PM\Tests\Unit\Main\Deep6'
        );
        $this->assertContains($expected, $list);

        $expected = array(
            'name'      => 'PM\Tests\Unit\Main\Deep3',
            'classname' => 'PM\Tests\Unit\Main\Deep3'
        );
        $this->assertContains($expected, $list);
    }

    /**
     * Success test for method has.
     *
     * @return void
     */
    public function testHas() {
        $this->assertFalse($this->_provider->has('PM\Tests\Unit\Main\Foo'));

        // create without alias
        $instance = $this->_provider->get('PM\Tests\Unit\Main\Foo');
        $this->assertTrue($this->_provider->has('PM\Tests\Unit\Main\Foo'));
        $this->assertFalse($this->_provider->has('Foo'));

        // set alias
        $this->_provider->set($instance, 'Foo');
        $this->assertTrue($this->_provider->has('Foo'));

        // reset instance with alias
        $this->_provider->reset('Foo');
        $this->assertFalse($this->_provider->has('Foo'));
        $this->assertFalse($this->_provider->has('PM\Tests\Unit\Main\Foo'));

        // create again without alias
        $this->_provider->get('PM\Tests\Unit\Main\Foo');
        $this->assertTrue($this->_provider->has('PM\Tests\Unit\Main\Foo'));
        $this->assertFalse($this->_provider->has('Foo'));

        // reset wihout alias
        $this->_provider->reset('PM\Tests\Unit\Main\Foo');
        $this->assertFalse($this->_provider->has('PM\Tests\Unit\Main\Foo'));
    }

    /**
     * Success test for method prototype.
     *
     * @return void
     */
    public function testPrototype() {
        $instance  = $this->_provider->get('PM\Tests\Unit\Main\Foo');
        $prototype = $this->_provider->prototype('PM\Tests\Unit\Main\Foo');
        $this->assertNotSame($instance, $prototype);

        $fooDep    = $this->_provider->get('PM\Tests\Unit\Main\FooDep');
        $prototype = $this->_provider->prototype('PM\Tests\Unit\Main\FooDep');
        $this->assertNotSame($instance, $prototype);
        $this->assertSame($fooDep->foo, $prototype->foo);

        $fooDep    = $this->_provider->get('PM\Tests\Unit\Main\FooDep');
        $prototype = $this->_provider->prototype('PM\Tests\Unit\Main\FooDep', true);
        $this->assertNotSame($instance, $prototype);
        $this->assertNotSame($fooDep->foo, $prototype->foo);

        $proDep  = $this->_provider->get('PM\Tests\Unit\Main\PrototypeDep');
        $proDep2 = $this->_provider->prototype('PM\Tests\Unit\Main\PrototypeDep');
        $this->assertNotSame($proDep, $proDep2);
        $this->assertSame($proDep->config, $proDep2->config);

        $proDep  = $this->_provider->get('PM\Tests\Unit\Main\PrototypeDep');
        $proDep2 = $this->_provider->prototype('PM\Tests\Unit\Main\PrototypeDep', true);
        $this->assertNotSame($proDep, $proDep2);
        $this->assertSame($proDep->config, $proDep2->config);
    }
}

/**
 * Class for tests.
 *
 * testGetSimple
 * testGetWithDependecies
 */
class Foo {

}

/**
 * Class for tests.
 *
 * testGetWithDependecies
 */
class FooDep {

    public function __construct(Foo $foo) {
        $this->foo = $foo;
    }
}

/**
 * Class for tests.
 *
 * testGetCycleSimple
 */
class CycleSelf {
    public function __construct(CycleSelf $self) {
    }
}

/**
 * Class for tests.
 *
 * testGetCycleInherit
 */
class CycleInherit {
    public function __construct(CycleSelf $self) {
    }
}

/**
 * Class for tests.
 *
 * testGetDeep10
 * testGetDeepMulti
 */
class Deep1 {

}

/**
 * Class for tests.
 *
 * testGetDeep10
 * testGetListinstances
 */
class Deep2 {
    public function __construct(Deep1 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 * testGetDeepMulti
 * testGetListinstances
 */
class Deep3 {
    public function __construct(Deep2 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 */
class Deep4 {
    public function __construct(Deep3 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 * testGetDeepMulti
 */
class Deep5 {
    public function __construct(Deep4 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 * testGetListinstances
 */
class Deep6 {
    public function __construct(Deep5 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 */
class Deep7 {
    public function __construct(Deep6 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 * testGetDeepMulti
 */
class Deep8 {
    public function __construct(Deep7 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 */
class Deep9 {
    public function __construct(Deep8 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeep10
 */
class Deep10 {
    public function __construct(Deep9 $deep) {$this->deep = $deep;}
}

/**
 * Class for tests.
 *
 * testGetDeepMulti
 */
class DeepMulti {
    public function __construct(Deep3 $deep3, Deep8 $deep8) {
        $this->deep3 = $deep3;
        $this->deep8 = $deep8;
    }
}

/**
 * Class for tests.
 *
 * testPrototype
 */
class PrototypeDep {
    public function __construct(\PM\Main\Config $config) {$this->config = $config;}
}