<?php

namespace PM\Tests\Unit\Main\Web\Controller;

use PM\Main\Abstracts\Unit\TestCase;
use PM\Main\Translate\Enum\Module;
use PM\Main\Translate\Enum\Lang;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Controller\Translate.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class TranslateTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\Controller\Translate
     */
    private $_controller;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        // mock instead of cahce instance
        $cache = $this->getMock('PM\Main\Abstracts\Entity', array('load', 'save'));
        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array()));

        $cache->expects($this->any())
            ->method('save')
            ->will($this->returnValue(array()));

        $this->getProvider()->set($cache, 'PM\Main\Cache');

        $this->_controller = $this->getProvider()->get('PM\Main\Web\Controller\Translate');
    }

    /**
     * Success test for action translate.
     *
     * @return void
     */
    public function testTranslateMain() {
        $payload  = $this->_controller->setParams(array('module' => Module::MAIN))->setAction('translate')->run()->getData();
        $expected = array(
            'lang' => Lang::CZECH,
            'translate' => array(
                'main.third' => 'Třetí'
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('translate', $payload);
        $this->assertCount(1, $payload['translate']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action translate.
     *
     * @return void
     */
    public function testTranslateTest() {
        $payload  = $this->_controller->setParams(array('module' => 'test'))->setAction('translate')->run()->getData();
        $expected = array(
            'lang' => Lang::CZECH,
            'translate' => array(
                'test.text.key' => 'Prostě nějaký text',
                'test.two.key'  => 'Překlad'
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('translate', $payload);
        $this->assertCount(2, $payload['translate']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action translateByLang.
     *
     * @return void
     */
    public function testTranslateWithLang() {
        $params = array(
            'module' => 'test',
            'lang' => Lang::ENGLISH
        );
        $payload  = $this->_controller->setParams($params)->setAction('translateByLang')->run()->getData();
        $expected = array(
            'lang' => Lang::ENGLISH,
            'translate' => array(
                'test.text.key' => 'Just some text',
                'test.two.key'  => 'Translate'
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('translate', $payload);
        $this->assertCount(2, $payload['translate']);
        $this->assertEquals($expected, $payload);
    }

    /**
     * Success test for action getLangs.
     *
     * @return void
     */
    public function testGetLangs() {
        $payload  = $this->_controller->setAction('getLangs')->run()->getData();
        $expected = array(
            'default' => Lang::CZECH,
            'langs' => array(
                array(
                    'value' => Lang::CZECH,
                    'name'  => 'main.language.cs'
                ),
                array(
                    'value' => Lang::ENGLISH,
                    'name'  => 'main.language.en'
                )
            )
        );

        $this->assertInternalType('array', $payload);
        $this->assertCount(2, $payload);
        $this->assertArrayHasKey('default', $payload);
        $this->assertArrayHasKey('langs', $payload);
        $this->assertEquals($expected, $payload);
    }
}
