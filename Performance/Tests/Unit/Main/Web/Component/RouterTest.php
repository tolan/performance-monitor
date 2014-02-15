<?php

namespace PF\Tests\Unit\Main\Web\Component;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Web\Component\Router.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class RouterTest extends TestCase {

    /**
     * request instance.
     *
     * @var \PF\Main\Web\Component\Router
     */
    private $_router;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_router = $this->getProvider()->get('PF\Main\Web\Component\Router');
    }

    /**
     * Success test for method route.
     *
     * @return void
     */
    public function testRoute() {
        $request = $this->getProvider()->get('request'); /* @var $request \PF\Main\Web\Component\Request */
        // set basic information for request
        $request->getServer()->set('REQUEST_URI', '/translate/langs');
        $request->getServer()->set('BASE', '');
        $request->getServer()->set('REQUEST_METHOD', 'GET');

        // mock instead of cahce instance
        $cache = $this->getMock('PF\Main\Abstracts\Entity');
        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array()));

        $this->getProvider()->set($cache, 'PF\Main\Cache');

        $this->_router->route();
    }

    /**
     * Success test for method getController.
     *
     * @return void
     */
    public function testGetController() {
        $request = $this->getProvider()->get('request'); /* @var $request \PF\Main\Web\Component\Request */
        // set basic information for request
        $request->getServer()->set('REQUEST_URI', '/translate/langs');
        $request->getServer()->set('BASE', '');
        $request->getServer()->set('REQUEST_METHOD', 'GET');

        // mock instead of cahce instance
        $cache = $this->getMock('PF\Main\Abstracts\Entity');
        $cache->expects($this->any())
            ->method('load')
            ->will($this->returnValue(array()));

        $this->getProvider()->set($cache, 'PF\Main\Cache');

        $controller = $this->_router->getController();

        $this->assertInstanceOf('PF\Main\Web\Controller\Translate', $controller);
        $this->assertEquals('GetLangs', $controller->getAction());
    }
}
