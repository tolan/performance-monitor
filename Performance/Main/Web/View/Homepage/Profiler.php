<?php

namespace PF\Main\Web\View\Homepage;

use PF\Main\Web\View\Html;

/**
 * This script defines class for homepage profiler view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Profiler extends Html {

    /**
     * Generates payload. Sets headers and body in template.
     *
     * @return \PF\Main\Web\View\Homepage\Index
     */
    public function getPayload() {
        parent::getPayload();
        $template = $this->getTemplate();
        $template->addScript('/js/app.js');
        $template->addScript('/js/directive.js');
        $template->addScript('/js/controller/Lang.js');
        $template->addScript('/js/controller/Profiler.js');
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
                    .'<div class="header">'
                        . '<div ng-controller="LangCtrl" class="lang">'
                            . '<div ng-include="template"></div>'
                        . '</div>'
                    .'</div>'
                    . '<div id="loader"></div>'
                    . '<div ng-controller="ProfilerBrowserCtrl" class="content" ng-include="template">'
                        . 'Application error.'
                    . '</div>'
                . '</div>';

        return $html;
    }
}