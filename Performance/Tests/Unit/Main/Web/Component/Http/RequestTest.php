<?php

namespace PF\Tests\Unit\Main\Web\Component\Http;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Web\Component\Http\Request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class RequestTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PF\Main\Web\Component\Http\Request
     */
    private $_entity;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_entity = $this->getProvider()->get('PF\Main\Web\Component\Http\Request');
    }

    /**
     * Simple test for instance.
     *
     * @return void
     */
    public function testInstance() {
        $this->assertInstanceOf('PF\Main\Web\Component\Http\Request', $this->_entity);
    }
}
