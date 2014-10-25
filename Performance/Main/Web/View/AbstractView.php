<?php

namespace PM\Main\Web\View;

use PM\Main\Web\Component\Template\AbstractTemplate;
use PM\Main\Web\Component\Request;

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
     * @var \PM\Main\Web\Component\Template\AbstractTemplate
     */
    private $_template = null;

    /**
     * Request instance.
     *
     * @var \PM\Main\Web\Component\Request
     */
    private $_request = null;

    /**
     * Contruct method.
     *
     * @param \PM\Main\Web\Component\Request $request Request instance
     */
    public function __construct(Request $request) {
        $this->_request = $request;
    }

    /**
     * Sets data for template.
     *
     * @param mixed $data Data for template
     *
     * @return \PM\Main\Web\View\AbstractView
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
     * @param \PM\Main\Web\Component\Template\AbstractTemplate $template Template instance
     *
     * @return \PM\Main\Web\View\AbstractView
     */
    final public function setTemplate(AbstractTemplate $template) {
        $this->_template = $template;

        return $this;
    }

    /**
     * Returns request instance.
     *
     * @return \PM\Main\Web\Component\Request
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
     * @return \PM\Main\Web\Component\Template\AbstractTemplate
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
