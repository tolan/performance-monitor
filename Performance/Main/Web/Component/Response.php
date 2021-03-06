<?php

namespace PM\Main\Web\Component;

use PM\Main\Web\View\AbstractView;

/**
 * This script defines class for response.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Response {

    /**
     * Instance of template
     *
     * @var \PM\Main\Web\Component\Template\AbstractTemplate
     */
    private $_template = null;

    /**
     * Sets templates instance.
     *
     * @param \PM\Main\Web\Component\Template\AbstractTemplate $template Template instance
     *
     * @return \PM\Main\Web\Component\Response
     */
    public function setTemplate(Template\AbstractTemplate $template) {
        $this->_template = $template;

        return $this;
    }

    /**
     * Sets view instance.
     *
     * @param \PM\Main\Web\View\AbstractView $view View instance
     *
     * @return \PM\Main\Web\Component\Response
     */
    public function setView(AbstractView $view) {
        $this->_template->setView($view);

        return $this;
    }

    /**
     * Sets data to template.
     *
     * @param mixed $data Data for display
     *
     * @return \PM\Main\Web\Component\Response
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
     * Echo function for payload. It flush whole output and close connection with browser.
     *
     * @return \PM\Main\Web\Component\Response
     */
    public function flush() {
        ob_end_clean();
        ob_start();

        echo $this->getPayload();

        header('Connection: close');
        header('Content-Length: '.ob_get_length());
        ob_end_flush();
        flush();

        return $this;
    }
}
