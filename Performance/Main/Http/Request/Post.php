<?php

namespace PM\Main\Http\Request;

use PM\Main\Http\Enum\Method;

/**
 * This script defines class for POST http request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Post extends AbstractRequest {

    /**
     * Method type.
     *
     * @var string
     */
    protected $_method = Method::POST;
}
