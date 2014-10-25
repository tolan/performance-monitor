<?php

namespace PM\Main\Web\Component\Template;

use PM\Main\Web\View\AbstractView;

/**
 * Abstract class for response template.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractTemplate {

    /**
     * Data which will be included to view
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * View instance
     *
     * @var \PM\Main\Web\View\AbstractView
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
     * @return \PM\Main\Web\Component\Template\AbstractTemplate
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
     * @param \PM\Main\Web\View\AbstractView $view View instance
     *
     * @return \PM\Main\Web\Component\Template\AbstractTemplate
     */
    final public function setView(AbstractView $view) {
        $this->_view = $view;

        return $this;
    }

    /**
     * Gets view instance.
     *
     * @return \PM\Main\Web\View\AbstractView
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
