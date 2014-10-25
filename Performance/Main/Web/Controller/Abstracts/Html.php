<?php

namespace PM\Main\Web\Controller\Abstracts;

/**
 * Abstract class for HTML controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Html extends Controller {

    /**
     * Return response instance
     *
     * @return \PM\Main\Web\Component\Response
     */
    final public function getResponse() {
        $template = $this->getProvider()->get('PM\Main\Web\Component\Template\Html');
        $response = $this->getProvider()->get('response')->setTemplate($template);
        return $response;
    }

    /**
     * Return view instance.
     *
     * @return \PM\Main\Web\View\Html
     */
    protected function getView() {
        $viewName = strtr(get_class($this), array('Controller' => 'View')).'\\'.$this->getAction();
        return $this->getProvider()->get($viewName);
    }
}
