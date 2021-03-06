<?php

namespace PM\Tests\Unit\Main\Web\View;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Web\View\Json.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class JsonTest extends TestCase {

    /**
     * App instance.
     *
     * @var \PM\Main\Web\View\Json
     */
    private $_view;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        parent::setUp();

        $this->_view = $this->getProvider()->get('PM\Main\Web\View\Json');
    }

    /**
     * Success test for method getData.
     *
     * @return void
     */
    public function testGetData() {
        $this->assertNull($this->_view->getData());

        $this->_view->setData(array('data'));
        $this->assertEquals(array('data'), $this->_view->getData());
    }

    /**
     * Success test for method setData.
     *
     * @return void
     */
    public function testSetData() {
        $this->assertInstanceOf('PM\Main\Web\View\Json', $this->_view->setData(array('data')));
        $this->assertEquals(array('data'), $this->_view->getData());
    }

    /**
     * Success test for method getPayload.
     *
     * @return void
     */
    public function testGetPayload() {
        $this->assertNull($this->_view->getPayload());

        $this->_view->setData(array('data'));
        $this->assertEquals(array('data'), $this->_view->getPayload());
    }

    /**
     * Success test for method setTemplate.
     *
     * @return void
     */
    public function testSetTemplate() {
        $template = $this->getProvider()->get('PM\Main\Web\Component\Template\Html');
        $this->assertInstanceOf('PM\Main\Web\View\Json', $this->_view->setTemplate($template));
    }
}
