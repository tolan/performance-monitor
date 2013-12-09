<?php

/**
 * Abstract class for global variables.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Web_Component_Http_Abstract extends Performance_Main_Abstract_Entity {

    /**
     * Construct method
     *
     * @throws Performance_Main_Web_Exception Throws when global variable doesn't exist.
     */
    public function __construct() {
        $class  = get_class($this);
        $global = strtoupper(substr($class, strrpos($class, '_')+1));
        switch ($global) {
            case 'COOKIE':
                $data = $_COOKIE;
                break;
            case 'ENV':
                $data = $_ENV;
                break;
            case 'FILES':
                $data = $_FILES;
                break;
            case 'GET':
                $data = $_GET;
                break;
            case 'POST':
                $data = $_POST;
                break;
            case 'REQUEST':
                $data = $_REQUEST;
                break;
            case 'SERVER':
                $data = $_SERVER;
                break;
            case 'SESSION':
                $data = $_SESSION;
                break;
            default :
                throw new Performance_Main_Web_Exception('Undefined global.');
        }

        $this->fromArray($data);
    }
}
