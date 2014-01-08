<?php

namespace PF\Main\Web\View;

use PF\Main\Web\Component\Template\AbstractTemplate;

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
