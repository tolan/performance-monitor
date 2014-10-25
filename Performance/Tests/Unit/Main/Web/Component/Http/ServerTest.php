<?php

namespace PM\Tests\Unit\Main\Web\Component\Http;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Component\Http\Server.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ServerTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\Component\Http\Server
     */
    private $_entity;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_entity = $this->getProvider()->get('PM\Main\Web\Component\Http\Server');
    }

    /**
     * Simple test for instance.
     *
     * @return void
     */
    public function testInstance() {
        $this->assertInstanceOf('PM\Main\Web\Component\Http\Server', $this->_entity);
    }
}
