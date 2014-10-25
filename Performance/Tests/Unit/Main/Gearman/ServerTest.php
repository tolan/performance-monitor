<?php

namespace PM\Tests\Unit\Main\Gearman;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Gearman\Server.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ServerTest extends TestCase {

    /**
     * Server instance.
     *
     * @var \PM\Main\Gearman\Server
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_instance = $this->getProvider()->get('PM\Main\Gearman\Server');

        parent::setUp();
    }

    /**
     * Success test for set message.
     *
     * @return void
     */
    public function testSetMessage() {
        $message = $this->getMock('PM\Main\Abstracts\Gearman\Message');
        $this->assertInstanceOf('PM\Main\Gearman\Server', $this->_instance->setMessage($message));
    }

    /**
     * Success test for get result.
     *
     * @return void
     */
    public function testGetResult() {
        $this->assertNull($this->_instance->getResult());
    }

    /**
     * Success test for run process on worker and return right result.
     *
     * @return void
     */
    public function testRun() {
        $worker      = $this->getMock('PM\Main\Abstracts\Gearman\Worker', array(), array($this->getProvider()));
        $workerClass = get_class($worker);
        $worker->expects($this->any())
            ->method('getResult')
            ->will($this->returnValue('test'));

        $message = $this->getMock('PM\Main\Abstracts\Gearman\Message');
        $message->expects($this->any())
            ->method('getTarget')
            ->will($this->returnValue($workerClass));
        $this->getProvider()->set($worker);

        $this->_instance->setMessage($message)->run();

        $this->assertEquals('test', $this->_instance->getResult());
    }
}
