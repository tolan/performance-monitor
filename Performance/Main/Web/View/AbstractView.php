<?php

namespace PF\Main\Web\View;

use PF\Main\Web\Component\Template\AbstractTemplate;
use PF\Main\Web\Component\Request;

/**
 * Abstract class for view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractView {

    /**
     * Data for template.
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * Template instance
     *
     * @var \PF\Main\Web\Component\Template\AbstractTemplate
     */
    private $_template = null;

    /**
     * Request instance.
     *
     * @var \PF\Main\Web\Component\Request
     */
    private $_request = null;

    /**
     * Contruct method.
     *
     * @param \PF\Main\Web\Component\Request $requesst Request instance
     */
    public function __construct(Request $requesst) {
        $this->_request = $requesst;
    }

    /**
     * Sets data for template.
     *
     * @param mixed $data Data for template
     *
     * @return \PF\Main\Web\View\AbstractView
     */
    final public function setData($data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * Gets data for template.
     *
     * @return mixed
     */
    final public function getData() {
        return $this->_data;
    }

    /**
     * Sets template instance.
     *
     * @param \PF\Main\Web\Component\Template\AbstractTemplate $template Template instance
     *
     * @return \PF\Main\Web\View\AbstractView
     */
    final public function setTemplate(AbstractTemplate $template) {
        $this->_template = $template;

        return $this;
    }

    /**
     * Returns request instance.
     *
     * @return \PF\Main\Web\Component\Request
     */
    protected function getRequest() {
        return $this->_request;
    }

    /**
     * Returns absolute path from reqeust.
     *
     * @return string
     */
    protected function getAbsolutePath() {
        $server  = $this->_request->getServer();
        $domain = $server->getSERVER_NAME();
        $path   = $server->getBASE();
        $protocol = strtolower(strstr($server->getSERVER_PROTOCOL(), '/', true));

        return $protocol.'://'.$domain.$path;
    }

    /**
     * Returns template instance.
     *
     * @return \PF\Main\Web\Component\Template\AbstractTemplate
     */
    protected function getTemplate() {
        return $this->_template;
    }

    /**
     * Generate payload in template.
     *
     * @return void
     */
    abstract public function getPayload();
}
