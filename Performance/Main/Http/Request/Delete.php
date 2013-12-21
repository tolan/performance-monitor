<?php

/**
 * This script defines class for DELETE http request.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Http_Request_Delete extends Performance_Main_Http_Request_Abstract {

    /**
     * Method type.
     * 
     * @var string
     */
    protected $_method = Performance_Main_Http_Enum_Method::DELETE;
}
