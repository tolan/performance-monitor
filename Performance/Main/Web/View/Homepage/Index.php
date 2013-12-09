<?php

/**
 * This script defines class for homepage index view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Web_View_Homepage_Index extends Performance_Main_Web_View_Html {

    /**
     * Generates payload. Sets headers and body in template.
     *
     * @return Performance_Main_Web_View_Homepage_Index
     */
    public function getPayload() {
        parent::getPayload();
        $template = $this->getTemplate();
        $template->addScript('/Performance/web/js/app.js');
        $template->addScript('/Performance/web/js/menu.js');
        $template->addScript('/Performance/web/js/controller/Profiler.js');
        $template->setBody($this->_generateHtml());

        return $this;
    }

    /**
     * Returns HTML content of body.
     *
     * @return string
     */
    private function _generateHtml() {
        $html = '<div ng-app="Perf">'
                    . '<div ng-controller="MenuCtrl" class="header"><div ng-include="template"></div></div>'
                    . '<div ng-view class="content">Application error.</div>'
                . '</div>';

        return $html;
    }
}