<?php

namespace PF\Main\Web\View;

/**
 * Abstract class for each HTML view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Html extends AbstractView {

    /**
     * Generate HTML payload. Adds libraries and syles.
     *
     * @return \PF\Main\Web\View\Html
     */
    public function getPayload() {
        $template = $this->getTemplate();
        $template->addHeaderTag('<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">');
        $template->addStyle('/Performance/web/css/bootstrap.css');
        $template->addStyle('/Performance/web/css/my.css');
        $template->addScript('/Performance/web/js/lib/jquery-2.0.3.js');
        $template->addScript('/Performance/web/js/lib/bootstrap.js');
        $template->addScript('/Performance/web/js/lib/angular.js');
        $template->addScript('/Performance/web/js/lib/angular-loader.js');
        $template->addScript('/Performance/web/js/lib/angular-resource.js');
        $template->addScript('/Performance/web/js/lib/angular-strap.js');
        $template->addScript('/Performance/web/js/lib/angular-route.js');
        $template->addScript('/Performance/web/js/lib/ui-bootstrap-tpls.js');
        $template->addScript('/Performance/web/js/lib/underscore.js');

        return $this;
    }

    /**
     * Returns HTML template instance.
     *
     * @return \PF\Main\Web\Component\Template\Html
     */
    final protected function getTemplate() {
        return parent::getTemplate();
    }
}
