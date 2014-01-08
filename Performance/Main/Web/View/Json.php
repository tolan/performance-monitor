<?php

namespace PF\Main\Web\View;

/**
 * Abstract class for each JSON view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Json extends AbstractView {

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
     * @return \PF\Main\Web\Component\Template\Json
     */
    final protected function getTemplate() {
        return parent::getTemplate();
    }
}
