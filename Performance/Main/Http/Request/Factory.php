<?php

namespace PM\Main\Http\Request;

use PM\Main\Http\Enum\Method;
use PM\Main\Http\Exception;

/**
 * Factory class for create request instance by given method type.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Factory {

    /**
     * Returns reqeust by method.
     *
     * @param enum $method One of \PM\Main\Http\Enum\Method
     *
     * @return \PM\Main\Http\Request\AbstractRequest
     *
     * @throws \PM\Main\Http\Exception Throws when request class doesn't exist.
     */
    public static function getInstance($method) {
        switch ($method) {
            case Method::DELETE:
                $instance = new Delete(null, $method);
                break;
            case Method::GET:
                $instance = new Get(null, $method);
                break;
            case Method::POST:
                $instance = new Post(null, $method);
                break;
            case Method::PUT:
                $instance = new Put(null, $method);
                break;
            default:
                throw new Exception('Unsupported method.');
        }

        return $instance;
    }
}
