<?php

namespace PM\Tests\Unit\Main\Web\Component;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Component\Router.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class RouterTest extends TestCase {

    /**
     * request instance.
     *
     * @var \PM\Main\Web\Component\Router
     */
    private $_router;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_router = $this->getProvider()->get('PM\Main\Web\Component\Router');
    }

    /**
     * Success test for method route.
     *
     * @return void
     */
    public function testRoute() {
        $request = $this->getProvider()->get('request'); /* @var $request \PM\Main\Web\Component\Request */
        // set basic information for request
        $request->getServer()->set('REQUEST_URI', '/menu');
        $request->getServer()->set('BASE', '');
        $request->getServer()->set('REQUEST_METHOD', 'GET');

        $info     = $this->_router->route()->getRouteInfo();
        $expected = array (
            'controller' => '\PM\Main\Web\Controller\Menu',
            'action'     => 'Index',
            'params'     => null,
            'annotation' => array()
        );

        $this->assertEquals($expected, $info);

        $request->getServer()->set('REQUEST_URI', '/');
        $info                   = $this->_router->reset()->route()->getRouteInfo();
        $expected['controller'] = '\PM\Main\Web\Controller\Homepage';
        $this->assertEquals($expected, $info);
    }

    /**
     * Success test for method getController.
     *
     * @return void
     */
    public function testGetController() {
        $request = $this->getProvider()->get('request'); /* @var $request \PM\Main\Web\Component\Request */
        // set basic information for request
        $request->getServer()->set('REQUEST_URI', '/menu');
        $request->getServer()->set('BASE', '');
        $request->getServer()->set('REQUEST_METHOD', 'GET');

        $controller = $this->_router->getController();

        $this->assertInstanceOf('PM\Main\Web\Controller\Menu', $controller);
        $this->assertEquals('Index', $controller->getAction());
    }
}
