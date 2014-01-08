<?php

namespace PF\Main\Web\Controller\Abstracts;

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
     * @return \PF\Main\Web\Component\Response
     */
    final public function getResponse() {
        $template = $this->getProvider()->get('PF\Main\Web\Component\Template\Json');
        $response = $this->getProvider()->get('response')->setTemplate($template);
        return $response;
    }

    /**
     * Returns view instance
     *
     * @return \PF\Main\Web\View\Json
     */
    protected function getView() {
        return $this->getProvider()->get('PF\Main\Web\View\Json');
    }
}
