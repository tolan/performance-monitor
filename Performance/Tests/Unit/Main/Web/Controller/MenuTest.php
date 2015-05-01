<?php

namespace PM\Tests\Unit\Main\Web\Controller;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Controller\Menu.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class MenuTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\Controller\Menu
     */
    private $_controller;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_controller = $this->getProvider()->get('PM\Main\Web\Controller\Menu');
    }

    /**
     * Success test for action index.
     *
     * @return void
     */
    public function testGetDataForIndex() {
        $payload  = $this->_controller->setAction('index')->run()->getData();
        $expected = array(
            array(
                'id'       => 1,
                'parentId' => 0,
                'order'    => 1,
                'text'     => 'main.menu.summary',
                'href'     => '#profiler/list'
            ),
            array(
                'id'       => 2,
                'parentId' => 0,
                'order'    => 2,
                'text'     => 'main.menu.search',
                'href'     => '#search'
            ),
            array(
                'id'       => 3,
                'parentId' => 0,
                'order'    => 3,
                'text'     => 'main.menu.statistics',
                'href'     => '#statistics'
            ),
            array(
                'id'       => 4,
                'parentId' => 0,
                'order'    => 4,
                'text'     => 'main.menu.optimalization',
                'href'     => '#measure/optimalization'
            ),
            array(
                'id'       => 5,
                'parentId' => 0,
                'order'    => 5,
                'text' => 'main.menu.setup',
                'submenu' => array(
                    array(
                        'id'       => 6,
                        'parentId' => 5,
                        'order'    => 1,
                        'text'     => 'main.menu.cron',
                        'href'     => '#/settings/cron'
                    ),
                    array(
                        'id'       => 7,
                        'parentId' => 5,
                        'order'    => 2,
                        'text'     => 'main.menu.about',
                        'href'     => '#/settings/about'
                    ),
                )
            )
        );

        $this->assertEquals($expected, $this->_toArrayPayload($payload));
    }

    /**
     * It converts payload to simple arrays.
     *
     * @param array $payload Result from index action.
     *
     * @return array
     */
    private function _toArrayPayload($payload) {
        $result = array();
        foreach ($payload as $item) {
            $result[] = $item->toArray();
        }

        return $result;
    }
}
