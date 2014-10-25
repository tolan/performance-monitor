<?php

namespace PM\Main\Web\Component\Template;

/**
 * This script defines template for JSON view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Json extends AbstractTemplate {

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
