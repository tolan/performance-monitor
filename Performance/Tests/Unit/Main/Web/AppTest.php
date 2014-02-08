<?php

namespace PF\Tests\Unit\Main\Web;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Web\App.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class AppTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PF\Main\Web\App
     */
    private $_app;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_app = $this->getProvider()->get('PF\Main\Web\App');
    }

    /**
     * Simple test for run application.
     *
     * @return void
     */
    public function testRun() {
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

        // mock instead of response instance
        $response = $this->getMock('PF\Main\Web\Component\Response');
        $response->expects($this->any())
            ->method('flush')
            ->will($this->returnValue(''));

        $this->getProvider()->set($response, 'PF\Main\Web\Component\Response');

        $app = $this->_app->run();

        $this->assertSame($this->_app, $app);
    }
}
