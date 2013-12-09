<?php

/**
 * Abstract class for response template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Web_Component_Template_Abstract {

    /**
     * Data which will be included to view
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * View instance
     *
     * @var Performance_Main_Web_View_Abstract
     */
    private $_view = null;

    /**
     * Returns data for display in view.
     *
     * @return mixed
     */
    final public function getData() {
        return $this->_data;
    }

    /**
     * Sets data for display in view.
     *
     * @param mixed $data Data for display
     *
     * @return Performance_Main_Web_Component_Template_Abstract
     */
    final public function setData($data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * Returns output to display.
     *
     * @return string
     */
    final public function getPayload() {
        return $this->generatePayload();
    }

    /**
     * Sets view for template.
     *
     * @param Performance_Main_Web_View_Abstract $view View instance
     *
     * @return Performance_Main_Web_Component_Template_Abstract
     */
    final public function setView(Performance_Main_Web_View_Abstract $view) {
        $this->_view = $view;

        return $this;
    }

    /**
     * Gets view instance.
     *
     * @return Performance_Main_Web_View_Abstract
     */
    final public function getView() {
        return $this->_view;
    }

    /**
     * Abstract function for generate output for display
     *
     * @return string
     */
    abstract protected function generatePayload();
}
