<?php

namespace PF\Tests\Unit\Main\Web\Controller;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Web\Controller\Menu.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class MenuTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PF\Main\Web\Controller\Menu
     */
    private $_controller;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_controller = $this->getProvider()->get('PF\Main\Web\Controller\Menu');
    }

    /**
     * Success test for action index.
     *
     * @return void
     */
    public function testGetDataForIndex() {
        $payload = $this->_controller->setAction('index')->run()->getData();
        $expected = array(
            array(
                'text' => 'main.menu.summary',
                'href' => '#profiler/list'
            ),
            array(
                'text' => 'main.menu.search',
                'href' => '#search'
            ),
            array(
                'text' => 'main.menu.statistics',
                'href' => '#statistics'
            ),
            array(
                'text' => 'main.menu.optimalization',
                'href' => '#measure/optimalization'
            ),
            array(
                'text' => 'main.menu.setup',
                'submenu' => array(
                    array(
                        'text' => 'main.menu.cron',
                        'href' => '#/settings/cron'
                    ),
                    array(
                        'text' => 'main.menu.about',
                        'href' => '#/settings/about'
                    ),
                )
            )
        );

        $this->assertEquals($expected, $payload);
    }
}
