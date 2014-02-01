<?php

namespace PF\Tests\Unit\Main\Event;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Event\Message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class MessageTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Event\Message
     */
    private $_message;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_message = $this->getProvider()->prototype('PF\Main\Event\Message');

        parent::setUp();
    }

    /**
     * Success test for set data to event message.
     *
     * @return void
     */
    public function testSetData() {
        $message = $this->_message->setData(array());

        $this->assertInstanceOf('PF\Main\Event\Message', $message);
        $this->assertEquals(array(), $message->getData());
    }

    /**
     * Success test for get data from event message.
     *
     * @return void
     */
    public function testGetData() {
        $this->assertEquals(null, $this->_message->getData());

        $this->_message->setData(new \stdClass());
        $this->assertInstanceOf('stdClass', $this->_message->getData());
    }
}
