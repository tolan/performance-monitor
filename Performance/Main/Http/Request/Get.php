<?php

/**
 * This script defines class for GET http request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Http_Request_Get extends Performance_Main_Http_Request_Abstract {

    /**
     * Method type.
     *
     * @var string
     */
    protected $_method = Performance_Main_Http_Enum_Method::GET;
}
