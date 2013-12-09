<?php

/**
 * Abstract class for HTML controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Controller_Abstract_Html extends Performance_Main_Web_Controller_Abstract {

    /**
     * Return response instance
     *
     * @return Performance_Main_Web_Component_Response
     */
    final public function getResponse() {
        return $this->getProvider()->get('Performance_Main_Web_Component_Response');
    }

    /**
     * Return view instance.
     *
     * @return Performance_Main_Web_View_Html
     */
    protected function getView() {
        $viewName = strtr(get_class($this), array('Controller' => 'View')).'_'.$this->getAction();
        return $this->getProvider()->get($viewName);
    }
}
