<?php

/**
 * Factory class for create request instance by given method type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Http_Request_Factory {

    /**
     * Returns reqeust by method.
     *
     * @param enum $method One of Performance_Main_Http_Enum_Method
     *
     * @return Performance_Main_Http_Request_Abstract
     *
     * @throws Performance_Main_Http_Exception Throws when request class doesn't exist.
     */
    public static function getInstance($method) {
        switch ($method) {
            case Performance_Main_Http_Enum_Method::DELETE:
                $instance = new Performance_Main_Http_Request_Delete(null, $method);
                break;
            case Performance_Main_Http_Enum_Method::GET:
                $instance = new Performance_Main_Http_Request_Get(null, $method);
                break;
            case Performance_Main_Http_Enum_Method::POST:
                $instance = new Performance_Main_Http_Request_Post(null, $method);
                break;
            case Performance_Main_Http_Enum_Method::DELETE:
                $instance = new Performance_Main_Http_Request_Delete(null, $method);
                break;
            default:
                throw new Performance_Main_Http_Exception('Unsupported method.');
        }

        return $instance;
    }
}
