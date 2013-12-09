<?php

/**
 * This script defines template for JSON view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Component_Template_Json extends Performance_Main_Web_Component_Template_Abstract {

    /**
     * Sets header and returns JSON encoded paylod.
     *
     * @return string
     */
    protected function generatePayload() {
        header('Content-type: text/json');
        header('Content-type: application/json');
        return json_encode($this->getView()->getPayload());
    }
}
