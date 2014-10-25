<?php

namespace PM\Tests\Unit\Main\Web\Component\Http;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Component\Http\Post.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class PostTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\Component\Http\Post
     */
    private $_entity;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_entity = $this->getProvider()->get('PM\Main\Web\Component\Http\Post');
    }

    /**
     * Simple test for instance.
     *
     * @return void
     */
    public function testInstance() {
        $this->assertInstanceOf('PM\Main\Web\Component\Http\Post', $this->_entity);
    }
}
