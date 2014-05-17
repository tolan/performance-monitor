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
        $path     = $this->getAbsolutePath();
        $template = $this->getTemplate();
        $template->addHeaderTag('<meta content="text/html; charset=UTF-8" http-equiv="Content-Type">');
        $template->addStyle($path.'/css/bootstrap.css');
        $template->addStyle($path.'/css/my.css');
        $template->addScript($path.'/js/lib/jquery-2.0.3.js');
        $template->addScript($path.'/js/lib/bootstrap.js');
        $template->addScript($path.'/js/lib/angular.js');
        $template->addScript($path.'/js/lib/angular-loader.js');
        $template->addScript($path.'/js/lib/angular-resource.js');
        $template->addScript($path.'/js/lib/angular-strap.js');
        $template->addScript($path.'/js/lib/angular-route.js');
        $template->addScript($path.'/js/lib/ui-bootstrap-tpls.js');
        $template->addScript($path.'/js/lib/underscore.js');
        $template->addHeaderTag('<script type="text/javascript">var base="'.$path.'"</script>');

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
