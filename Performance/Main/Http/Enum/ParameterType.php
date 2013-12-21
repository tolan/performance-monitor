<?php

/**
 * This class defines methods of parameters on http requests.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Http_Enum_ParameterType extends Performance_Main_Abstract_Enum {
    const DELETE = Performance_Main_Http_Enum_Method::DELETE;
    const GET    = Performance_Main_Http_Enum_Method::GET;
    const POST   = Performance_Main_Http_Enum_Method::POST;
    const PUT    = Performance_Main_Http_Enum_Method::PUT;

    /**
     * Map for allowed parameters on each request type.
     *
     * @var array
     */
    private static $_allowedParams = array(
        Performance_Main_Http_Enum_Method::DELETE => array(
            self::GET
        ),
        Performance_Main_Http_Enum_Method::GET => array(
            self::GET
        ),
        Performance_Main_Http_Enum_Method::POST => array(
            self::GET,
            self::POST
        ),
        Performance_Main_Http_Enum_Method::PUT => array(
            self::GET,
            self::POST
        )
    );

    /**
     * Returns allowed parameter types by given method type of request.
     *
     * @param type $method
     * @return type
     * @throws Performance_Main_Http_Exception
     */
    public static function getAllowedParams($method = null) {
        if ($method === null) {
            return self::$_allowedParams;
        }

        if (!isset(self::$_allowedParams[$method])) {
            throw new Performance_Main_Http_Exception('Undefined method parameters.');
        }

        return self::$_allowedParams[$method];
    }
}