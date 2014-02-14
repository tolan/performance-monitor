<?php

namespace PF\Tests\Unit\Main\Web\Component;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Web\Component\Response.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class ResponseTest extends TestCase {

    /**
     * request instance.
     *
     * @var \PF\Main\Web\Component\Response
     */
    private $_response;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_response = $this->getProvider()->get('PF\Main\Web\Component\Response');
    }

    /**
     * Success test for method getPayload.
     *
     * @return void
     */
    public function testGetPayload() {
        $data = 'myTest';
        $view = $this->getProvider()->get('PF\Main\Web\View\Json')->setData($data);

        $template = $this->getMock('PF\Main\Web\Component\Template\AbstractTemplate', array('generatePayload'));
        $template->setView($view);
        $template->expects($this->once())
            ->method('generatePayload')
            ->will($this->returnValue(json_encode($template->getView()->getPayload())));

        $this->_response->setTemplate($template)->setView($view);

        $test = $this->_response->getPayload();

        $this->assertSame(json_encode($data), $test);
    }
}
