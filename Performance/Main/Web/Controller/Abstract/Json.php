<?php

/**
 * Abstract class for JSON controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Controller_Abstract_Json extends Performance_Main_Web_Controller_Abstract {

    /**
     * Returns response with JSON template.
     *
     * @return Performance_Main_Web_Component_Response
     */
    final public function getResponse() {
        $template = $this->getProvider()->get('Performance_Main_Web_Component_Template_Json');
        $response = $this->getProvider()->get('Performance_Main_Web_Component_Response')->setTemplate($template);
        return $response;
    }

    /**
     * Returns view instance
     * 
     * @return Performance_Main_Web_View_Json
     */
    protected function getView() {
        return $this->getProvider()->get('Performance_Main_Web_View_Json');
    }
}
