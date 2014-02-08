<?php

namespace PF\Tests\Unit\Main\Event;

use PF\Main\Abstracts\Unit\TestCase;
use PF\Main\Event;

/**
 * This script defines class for php unit test case of class \PF\Main\Event\Manager.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ManagerTest extends TestCase {

    /**
     * Manager instance.
     *
     * @var \PF\Main\Event\Manager
     */
    private $_manager;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_manager = $this->getProvider()->get('PF\Main\Event\Manager');
        $this->_manager->clean();

        parent::setUp();
    }

    /**
     * Success test for get all listeners of event manager instance.
     *
     * @return void
     */
    public function testGetListener() {
        $manager   = $this->_manager->on('test', function() {});
        $listeners = $manager->getListeners();

        $this->assertCount(1, $listeners);
        $this->assertInstanceOf('PF\Main\Event\Listener\On', current($listeners));

        $manager   = $this->_manager->once('test', function() {});
        $listeners = $manager->getListeners();

        $this->assertCount(2, $listeners);
    }

    /**
     * Success test for get all events of event manager instance before flush.
     *
     * @return void
     */
    public function testGetEvent() {
        $manager = $this->_manager->emit('test');
        $events  = $manager->getEvents();

        $this->assertCount(1, $events);
        $this->assertInstanceOf('PF\Main\Event\Action\Emit', current($events));

        $manager->emit('event');
        $events = $manager->getEvents();

        $this->assertCount(2, $events);
    }

    /**
     * Success test for fire event and catch it by listener with function "on".
     *
     * @return void
     */
    public function testOn() {
        $message = $this->_createMessage();

        $this->_manager->on('test', function(Event\Interfaces\Message $message) {
            $message->getData()->var = 'aaa';
        })->emit('test', $message)->flush();

        $this->assertObjectHasAttribute('var', $message->getData());
        $this->assertEquals('aaa', $message->getData()->var);
    }

    /**
     * Success test for fire event and catch it by listener with function "on" 10 times.
     *
     * @return void
     */
    public function testOnMulti() {
        $class    = new \stdClass();
        $class->i = 0;

        $message = new Event\Message();
        $message->setData($class);

        $manager = $this->_manager->on('test', function(Event\Interfaces\Message $message) {
            $message->getData()->var = 'aaa';
            $message->getData()->i++;
        });

        $this->assertObjectNotHasAttribute('var', $class);

        for($i = 0; $i < 10; $i++) {
            $manager->emit('test', $message);
        }

        $this->assertObjectNotHasAttribute('var', $class);
        $manager->flush();

        $this->assertObjectHasAttribute('var', $class);
        $this->assertEquals('aaa', $class->var);
        $this->assertObjectHasAttribute('i', $class);
        $this->assertEquals(10, $class->i);
    }

    /**
     * Success test for fire event and catch it by listener with function "once".
     *
     * @return void
     */
    public function testOnce() {
        $message = $this->_createMessage();

        $this->_manager->once('test', function(Event\Interfaces\Message $message) {
            $message->getData()->var = 'aaa';
        })->emit('test', $message)->flush();

        $this->assertObjectHasAttribute('var', $message->getData());
        $this->assertEquals('aaa', $message->getData()->var);
    }

    /**
     * Success test for fire event and catch it by listener with function "once" only one time.
     *
     * @return void
     */
    public function testOnceMulti() {
        $class    = new \stdClass();
        $class->i = 0;

        $message = new Event\Message();
        $message->setData($class);

        $manager = $this->_manager->once('test', function(Event\Interfaces\Message $message) {
            $message->getData()->var = 'aaa';
            $message->getData()->i++;
        });

        $this->assertObjectNotHasAttribute('var', $class);

        for($i = 0; $i < 10; $i++) {
            $manager->emit('test', $message);
        }

        $this->assertObjectNotHasAttribute('var', $class);
        $manager->flush();

        $this->assertObjectHasAttribute('var', $class);
        $this->assertEquals('aaa', $class->var);
        $this->assertObjectHasAttribute('i', $class);
        $this->assertEquals(1, $class->i);
    }

    /**
     * Success test for clean all events and listeners in event manager.
     *
     * @return void
     */
    public function testClean() {
        $manager = $this->_manager->on('test', function() {})->emit('test');

        $manager->clean();

        $this->assertCount(0, $manager->getListeners());
        $this->assertCount(0, $manager->getEvents());
    }

    /**
     * Success test for fire some event and catch it by listener with function "on" and event name "all".
     *
     * @return void
     */
    public function testEventAll() {
        $message = $this->_createMessage();

        $this->_manager->on('all', function(Event\Interfaces\Message $message) {
            $message->getData()->var = 'aaa';
        })->emit('test', $message)->flush();

        $this->assertObjectHasAttribute('var', $message->getData());
        $this->assertEquals('aaa', $message->getData()->var);
    }

    /**
     * Success test for fire broadcast event and catch it by listener with function "on".
     *
     * @return void
     */
    public function testBroadcast() {
        $message = $this->_createMessage();

        $this->_manager->on('test', function(Event\Interfaces\Message $message) {
            $message->getData()->var = 'aaa';
        })->broadcast('test', $message)->flush();

        $this->assertObjectHasAttribute('var', $message->getData());
        $this->assertEquals('aaa', $message->getData()->var);
    }

    /**
     * Success test for fire event and catch it by listener. Here is tested many events and listeners and their catch function.
     *
     * @return void
     */
    public function testEventRouting() {
        $message = $this->_createMessage();
        $message->getData()->i = 0;
        $manager = $this->_manager;

        $manager->on('test', function(Event\Interfaces\Message $message) {
                $message->getData()->i++;
            })
            ->broadcast('test', $message)
            ->broadcast('event', $message)
            ->flush();

        $this->assertEquals(1, $message->getData()->i);

        $manager->emit('test', $message)
                ->flush();

        $this->assertEquals(2, $message->getData()->i);

        $manager->on('event', function(Event\Interfaces\Message $message) {
                $message->getData()->i = 20;
            })
            ->emit('test', $message)
            ->emit('event', $message)
            ->emit('test', $message)
            ->flush();
        $this->assertEquals(21, $message->getData()->i);
    }

    /**
     * Success test for fire event and catch it by listener. Here is tested many events and one listener and their catch function.
     *
     * @return void
     */
    public function testEventRoutingOnMulti() {
        $message = $this->_createMessage();
        $message->getData()->i = 0;
        $this->_manager->on('test event', function(Event\Interfaces\Message $message) {
                $message->getData()->i++;
            })
            ->broadcast('test', $message)
            ->broadcast('event', $message)
            ->broadcast('nothing', $message)
            ->flush();

        $this->assertEquals(2, $message->getData()->i);
    }

    /**
     * Success test for fire event and catch it by listener. Here is tested many events and one listener and their catch function.
     *
     * @return void
     */
    public function testEventRoutingOnceMulti() {
        $message = $this->_createMessage();
        $message->getData()->i = 0;
        $this->_manager->once('test event', function(Event\Interfaces\Message $message) {
                $message->getData()->i++;
            })
            ->broadcast('test', $message)
            ->broadcast('event', $message)
            ->broadcast('nothing', $message)
            ->flush();

        $this->assertEquals(1, $message->getData()->i);
    }

    /**
     * Helper method for create message.
     *
     * @param mixed $content Content for message
     *
     * @return \PF\Main\Event\Message
     */
    private function _createMessage($content = null) {
        $message = new Event\Message();

        if ($content === null) {
            $content = new \stdClass();
        }

        $message->setData($content);

        return $message;
    }
}
