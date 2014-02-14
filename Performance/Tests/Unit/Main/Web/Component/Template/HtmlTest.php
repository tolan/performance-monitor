<?php

namespace PF\Tests\Unit\Main\Web\Component\Template;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Web\Component\Template\Html.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class HtmlTest extends TestCase {

    /**
     * Template instance.
     *
     * @var \PF\Main\Web\Component\Template\Html
     */
    private $_template;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_template =  $this->getProvider()->get('PF\Main\Web\Component\Template\Html');
        $view = $this->getMock('PF\Main\Web\View\AbstractView');
        $view->expects($this->any())
            ->method('getPayload')
            ->will($this->returnValue($view));

        $this->_template->setView($view);
    }

    /**
     * Success test for method getPayload without body.
     *
     * @return void
     */
    public function testGetPayloadEmpty() {
        $payload = $this->_template->getPayload();

        $this->assertInternalType('string', $payload);
        $this->assertEquals('<!DOCTYPE html><html lang="en"><head></head><body></body></html>', $payload);
    }

    /**
     * Success test for method getPayload.
     *
     * @return void
     */
    public function testGetPayload() {
        $this->_template->addStyle('style.css');
        $payload = $this->_template->getPayload();

        $this->assertInternalType('string', $payload);
        $this->assertEquals(
            '<!DOCTYPE html><html lang="en"><head><link type="text/css" rel="stylesheet" href="style.css" /></head><body></body></html>',
            $payload
        );
    }

    /**
     * Success test for method getData and setData.
     *
     * @return void
     */
    public function testData() {
        $data = $this->_template->getData();

        $this->assertEmpty($data);

        $this->_template->setData(array('data'));
        $data = $this->_template->getData();

        $this->assertEquals(array('data'), $data);
    }

    /**
     * Success test for method getView.
     *
     * @return void
     */
    public function testGetView() {
        $this->assertInstanceOf('PF\Main\Web\View\AbstractView', $this->_template->getView());
    }

    /**
     * Success test for method setBody.
     *
     * @return void
     */
    public function testSetBody() {
        $this->_template->setBody('<div>Body</div>');

        $payload = $this->_template->getPayload();

        $this->assertEquals('<!DOCTYPE html><html lang="en"><head></head><body><div>Body</div></body></html>', $payload);
    }

    /**
     * Success test for method addHeaderTag.
     *
     * @return void
     */
    public function testAddHeaderTag() {
        $this->_template->addHeaderTag('<meta>test</meta>');

        $payload = $this->_template->getPayload();

        $this->assertEquals('<!DOCTYPE html><html lang="en"><head><meta>test</meta></head><body></body></html>', $payload);
    }

    /**
     * Success test for method addScript.
     *
     * @return void
     */
    public function testAddScript() {
        $this->_template->addScript('script.js');

        $payload = $this->_template->getPayload();

        $this->assertEquals(
            '<!DOCTYPE html><html lang="en"><head><script type="text/javascript" src="script.js" ></script></head><body></body></html>',
            $payload
        );
    }

    /**
     * Success test for method addStyle.
     *
     * @return void
     */
    public function testAddStyle() {
        $this->_template->addStyle('myStyle.css');

        $payload = $this->_template->getPayload();

        $this->assertEquals(
            '<!DOCTYPE html><html lang="en"><head><link type="text/css" rel="stylesheet" href="myStyle.css" /></head><body></body></html>',
            $payload
        );
    }
}
