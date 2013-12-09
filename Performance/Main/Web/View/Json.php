<?php

/**
 * Abstract class for each JSON view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_View_Json extends Performance_Main_Web_View_Abstract {

    /**
     * Generates payload.
     *
     * @return mixed
     */
    public function getPayload() {
        return $this->getData();
    }

    /**
     * Returns JSON template.
     *
     * @return Performance_Main_Web_Component_Template_Json
     */
    final protected function getTemplate() {
        return parent::getTemplate();
    }
}
