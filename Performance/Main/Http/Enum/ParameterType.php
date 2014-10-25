<?php

namespace PM\Main\Http\Enum;

use PM\Main\Abstracts\Enum;
use PM\Main\Http\Exception;

/**
 * This class defines methods of parameters on http requests.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class ParameterType extends Enum {
    const DELETE = Method::DELETE;
    const GET    = Method::GET;
    const POST   = Method::POST;
    const PUT    = Method::PUT;

    /**
     * Map for allowed parameters on each request type.
     *
     * @var array
     */
    private static $_allowedParams = array(
        Method::DELETE => array(
            self::GET
        ),
        Method::GET => array(
            self::GET
        ),
        Method::POST => array(
            self::GET,
            self::POST
        ),
        Method::PUT => array(
            self::GET,
            self::POST
        )
    );

    /**
     * Returns allowed parameter types by given method type of request.
     *
     * @param enum $method One of \PM\Main\Http\Enum\Method
     *
     * @return array
     *
     * @throws \PM\Main\Http\Exception
     */
    public static function getAllowedParams($method = null) {
        if ($method === null) {
            return self::$_allowedParams;
        }

        if (!isset(self::$_allowedParams[$method])) {
            throw new Exception('Undefined method parameters.');
        }

        return self::$_allowedParams[$method];
    }
}