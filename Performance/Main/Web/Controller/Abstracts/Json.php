<?php

namespace PM\Main\Web\Controller\Abstracts;

/**
 * Abstract class for JSON controller.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Json extends Controller {

    /**
     * Returns response with JSON template.
     *
     * @return \PM\Main\Web\Component\Response
     */
    final public function getResponse() {
        $template = $this->getProvider()->get('PM\Main\Web\Component\Template\Json');
        $response = $this->getProvider()->get('response');
        $response->setTemplate($template);
        return $response;
    }

    /**
     * Returns view instance
     *
     * @return \PM\Main\Web\View\Json
     */
    protected function getView() {
        return $this->getProvider()->get('PM\Main\Web\View\Json');
    }
}
