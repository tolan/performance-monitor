<?php

/**
 * This script defines class for response.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Component_Response {

    /**
     * Instance of template
     *
     * @var Performance_Main_Web_Component_Template_Abstract
     */
    private $_template = null;

    /**
     * Construct method
     *
     * @param Performance_Main_Web_Component_Template_Html $template Template instance
     */
    public function __construct(Performance_Main_Web_Component_Template_Html $template = null) {
        $this->_template = $template;
    }

    /**
     * Sets templates instance.
     *
     * @param Performance_Main_Web_Component_Template_Abstract $template Template instance
     *
     * @return Performance_Main_Web_Component_Response
     */
    public function setTemplate(Performance_Main_Web_Component_Template_Abstract $template) {
        $this->_template = $template;

        return $this;
    }

    /**
     * Sets view instance.
     *
     * @param Performance_Main_Web_View_Abstract $view View instance
     *
     * @return Performance_Main_Web_Component_Response
     */
    public function setView(Performance_Main_Web_View_Abstract $view) {
        $this->_template->setView($view);

        return $this;
    }

    /**
     * Sets data to template.
     *
     * @param mixed $data Data for display
     *
     * @return Performance_Main_Web_Component_Response
     */
    public function setData($data) {
        $this->_template->setData($data);

        return $this;
    }

    /**
     * Returns payload to display.
     *
     * @return string
     */
    public function getPayload() {
        return $this->_template->getPayload();
    }

    /**
     * Echo function for payload.
     *
     * @return Performance_Main_Web_Component_Response
     */
    public function flush() {
        $html = $this->getPayload();

        echo $html;

        return $this;
    }
}
