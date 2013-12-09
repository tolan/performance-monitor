<?php

/**
 * Abstract class for view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Web_View_Abstract {

    /**
     * Data for template.
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * Template instance
     *
     * @var Performance_Main_Web_Component_Template_Abstract
     */
    private $_template = null;

    /**
     * Sets data for template.
     *
     * @param mixed $data Data for template
     *
     * @return Performance_Main_Web_View_Abstract
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
     * @param Performance_Main_Web_Component_Template_Abstract $template Template instance
     *
     * @return Performance_Main_Web_View_Abstract
     */
    final public function setTemplate(Performance_Main_Web_Component_Template_Abstract $template) {
        $this->_template = $template;

        return $this;
    }

    /**
     * Returns template instance.
     *
     * @return Performance_Main_Web_Component_Template_Abstract
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
