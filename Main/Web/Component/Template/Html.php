<?php

/**
 * This script defines template for HTML view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_Component_Template_Html extends Performance_Main_Web_Component_Template_Abstract {

    /**
     * Items in HTML header
     *
     * @var array
     */
    private $_headers = array();

    /**
     * HTML string of body
     *
     * @var string
     */
    private $_body = null;

    /**
     * Method for add style file into HTML head.
     *
     * @param string $stylePath Path of file with styles.
     *
     * @return Performance_Main_Web_Component_Template_Html
     */
    public function addStyle($stylePath) {
        $this->_headers[] = '<link type="text/css" rel="stylesheet" href="'.$stylePath.'" />';

        return $this;
    }

    /**
     * Method for add javascript file into HTML head.
     *
     * @param string $sciptPath Path of file with javasscript.
     *
     * @return Performance_Main_Web_Component_Template_Html
     */
    public function addScript($sciptPath) {
        $this->_headers[] = '<script type="text/javascript" src="'.$sciptPath.'" ></script>';

        return $this;
    }

    /**
     * Method for add element into HTML head.
     *
     * @param string $element Whole HTML string for head item
     *
     * @return Performance_Main_Web_Component_Template_Html
     */
    public function addHeaderTag($element) {
        $this->_headers[] = $element;

        return $this;
    }

    /**
     * Sets HTML string of body.
     *
     * @param string $bodyHtml HTML string of body
     *
     * @return Performance_Main_Web_Component_Template_Html
     */
    public function setBody($bodyHtml) {
        $this->_body = $bodyHtml;

        return $this;
    }

    /**
     * Generate and returns HTML payload string.
     *
     * @return string HTML string
     */
    protected function generatePayload() {
        $this->getView()
            ->setTemplate($this)
            ->getPayload();

        return $this->_generateHtml();
    }

    /**
     * Generate HTML payload string.
     *
     * @return string
     */
    private function _generateHtml() {
        $html = '<!DOCTYPE html>';
        $html .= '<html lang="en">';
        $html .= $this->_generateHeader();
        $html .= $this->_generateBody();
        $html .= '</html>';

        return $html;
    }

    /**
     * Generate header part.
     *
     * @return string
     */
    private function _generateHeader() {
        return '<head>'.join('', $this->_headers).'</head>';
    }

    /**
     * Generate body part.
     *
     * @return string
     */
    private function _generateBody() {
        return '<body>'.$this->_body.'</body>';
    }
}