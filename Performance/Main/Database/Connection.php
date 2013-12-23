<?php

/**
 * This script defines class for connection to MySQL over PDO.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Database_Connection extends PDO {

    /**
     * Construct method.
     *
     * @param string $host     IP address or network name of MySQL database server.
     * @param string $username Username for connect to MySQL server
     * @param string $passwd   Password for connect to MySQL server
     * @param string $database Name of connected database
     * @param array  $options  Options for PDO
     *
     * @return void
     */
    public function __construct($host, $username, $passwd, $database=null, $options=array()) {
        $dsn = 'mysql:host='.$host.($database ? ';dbname='.$database : '');

        parent::__construct($dsn, $username, $passwd, $options);
    }
}
