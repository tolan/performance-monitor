<?php

namespace PF\Main\Web\View\Homepage;

use PF\Main\Web\View\Html;

/**
 * This script defines class for homepage index view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Index extends Html {

    /**
     * Generates payload. Sets headers and body in template.
     *
     * @return \PF\Main\Web\View\Homepage\Index
     */
    public function getPayload() {
        parent::getPayload();
        $template = $this->getTemplate();
        $template->addScript('/Performance/web/js/app.js');
        $template->addScript('/Performance/web/js/router.js');
        $template->addScript('/Performance/web/js/directive.js');
        $template->addScript('/Performance/web/js/controller/Menu.js');
        $template->addScript('/Performance/web/js/controller/Lang.js');
        $template->addScript('/Performance/web/js/controller/Profiler.js');
        $template->addScript('/Performance/web/js/controller/Search.js');
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
                        . '<div ng-controller="MenuCtrl" class="menu"><div ng-include="template"></div></div>'
                        . '<div ng-controller="LangCtrl" class="lang"><div ng-include="template"></div></div>'
                    .'</div>'
                    . '<div id="loader"></div>'
                    . '<div ng-view class="content">Application error.</div>'
                . '</div>';

        return $html;
    }
}