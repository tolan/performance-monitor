<?php

namespace PM\Tests\Unit\Main\Web\Component;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\Component\Request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class RequestTest extends TestCase {

    /**
     * request instance.
     *
     * @var \PM\Main\Web\Component\Request
     */
    private $_request;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_request = $this->getProvider()->get('PM\Main\Web\Component\Request');
    }

    /**
     * Success test for method getCookie.
     *
     * @return void
     */
    public function testGetCookie() {
        $cookie = $this->_request->getCookie();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Cookie', $cookie);
        $this->assertEmpty($cookie->toArray());
    }

    /**
     * Success test for method getEnv.
     *
     * @return void
     */
    public function testGetEnv() {
        $env = $this->_request->getEnv();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Env', $env);
        $this->assertEmpty($env->toArray());
    }

    /**
     * Success test for method getFiles.
     *
     * @return void
     */
    public function testGetFiles() {
        $files = $this->_request->getFiles();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Files', $files);
        $this->assertEmpty($files->toArray());
    }

    /**
     * Success test for method getGet.
     *
     * @return void
     */
    public function testGetGet() {
        $get = $this->_request->getGet();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Get', $get);
        $this->assertEmpty($get->toArray());
    }

    /**
     * Success test for method getInput.
     *
     * @return void
     */
    public function testGetInput() {
        $input = $this->_request->getInput();

        $this->assertEmpty($input);
    }

    /**
     * Success test for method getPost.
     *
     * @return void
     */
    public function testGetPost() {
        $post = $this->_request->getPost();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Post', $post);
        $this->assertEmpty($post->toArray());
    }

    /**
     * Success test for method getRequest.
     *
     * @return void
     */
    public function testGetRequest() {
        $request = $this->_request->getRequest();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Request', $request);
        $this->assertEmpty($request->toArray());
    }

    /**
     * Success test for method getServer.
     *
     * @return void
     */
    public function testGetServer() {
        $server = $this->_request->getServer();

        $this->assertInstanceOf('PM\Main\Web\Component\Http\Server', $server);
    }
}
