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
        $path     = $this->getAbsolutePath();
        $template = $this->getTemplate();
        $template->addScript($path.'/js/app.js');
        $template->addScript($path.'/js/router.js');
        $template->addScript($path.'/js/service.js');
        $template->addScript($path.'/js/filter.js');
        $template->addScript($path.'/js/directive.js');
        $template->addScript($path.'/js/service/Statistics.js');
        $template->addScript($path.'/js/controller/Menu.js');
        $template->addScript($path.'/js/controller/Lang.js');
        $template->addScript($path.'/js/controller/Profiler.js');
        $template->addScript($path.'/js/controller/Search.js');
        $template->addScript($path.'/js/controller/Statistics.js');

        $template->addHeaderTag('<title>PF</title>');

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
                        .'<div ng-controller="MenuCtrl" class="menu" ng-include="template"></div>'
                    .'</div>'
                    .'<div id="loader"></div>'
                    .'<div ng-view class="content">Application error.</div>'
                .'</div>';

        return $html;
    }
}