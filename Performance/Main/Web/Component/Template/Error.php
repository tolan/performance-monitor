<?php

namespace PF\Main\Web\Component\Template;

/**
 * This script defines template for error state of HTML view.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Error extends AbstractTemplate {

    /**
     * Generate HTML header with error code by data code.
     *
     * @return string
     */
    protected function generatePayload() {
        $exception = $this->getData(); /* @var $exception \PF\Main\Exception */
        $code      = 500;

        if ($exception->getCode() !== 0) {
            $code = $exception->getCode();
        }

        http_response_code($code);
    }
}
