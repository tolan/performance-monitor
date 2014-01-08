<?php

namespace PF\Main\Http\Request;

use PF\Main\Http\Enum\Method;

/**
 * This script defines class for DELETE http request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Delete extends AbstractRequest {

    /**
     * Method type.
     *
     * @var string
     */
    protected $_method = Method::DELETE;
}
