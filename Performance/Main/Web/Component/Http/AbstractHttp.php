<?php

namespace PM\Main\Web\Component\Http;

use PM\Main\Abstracts\Entity;
use PM\Main\Web\Exception;

/**
 * Abstract class for global variables.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractHttp extends Entity {

    /**
     * Construct method
     *
     * @throws \PM\Main\Web\Exception Throws when global variable doesn't exist.
     */
    public function __construct() {
        $class  = get_class($this);
        $global = strtoupper(substr($class, strrpos($class, '\\')+1));
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
                session_start();
                $data = $_SESSION;
                break;
            default :
                throw new Exception('Undefined global.');
        }

        $this->fromArray($data);
    }
}
