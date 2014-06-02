<?php

namespace PF\Main;

use PF\Main\Log;

/**
 * This script defines class for connect to MySQL database and provide basic function.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Database {

    /**
     * MySQL Server address
     *
     * @var string
     */
    private $_address = null;

    /**
     * MySQL user
     *
     * @var string
     */
    private $_user = null;

    /**
     * Password for MySQL user
     *
     * @var string
     */
    private $_password = null;

    /**
     * Name of database
     *
     * @var string
     */
    private $_database = null;

    /**
     * Flag that database is connected
     *
     * @var boolean
     */
    private $_isConnected = false;

    /**
     * Instance for transaction control.
     *
     * @var \PF\Main\Database\Transaction
     */
    private $_transaction = null;

    /**
     * Required options from configuration
     *
     * @var array
     */
    private $_configParams = array(
        'address',
        'user',
        'password',
        'database',
        'install'
    );

    /**
     * Connection to database.
     *
     * @var \PF\Main\Database\Connection
     */
    private $_connection;

    /**
     * Logger instance.
     *
     * @var \PF\Main\Log
     */
    private $_logger;

    /**
     * Construct method which sets parameters to database connection.
     *
     * @param \PF\Main\Config $config Configuration
     * @param \PF\Main\Log    $logger Logger instance
     *
     * @throws \PF\Main\Database\Exception Throws when missing some configuration option
     */
    public function __construct(Config $config, Log $logger) {
        $configuration = $config->get('database');

        if (count(array_diff($this->_configParams, array_keys($configuration))) > 0) {
            throw new Database\Exception('Wrong configuration. Requested options: '.join(', ', $this->_configParams));
        }

        $this->_address  = $configuration['address'];
        $this->_user     = $configuration['user'];
        $this->_password = $configuration['password'];
        $this->_database = $configuration['database'];
        $this->_logger   = $logger;

        if ($configuration['install'] === true) {
            $this->_install();
        }
    }

    /**
     * This method provides connection to database.
     *
     * @return \PF\Main\Database
     */
    public function connect() {
        if ($this->_isConnected === false) {
            $options = array(
                Database\Connection::ATTR_ERRMODE            => Database\Connection::ERRMODE_EXCEPTION,
                Database\Connection::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET UTF8; SET NAMES UTF8"
            );
            $this->_connection  = new Database\Connection($this->_address, $this->_user, $this->_password, $this->_database, $options);
            $this->_isConnected = true;
        }

        return $this;
    }

    /**
     * Returns PDO connection to database. It is for create custom statements.
     *
     * @return \PF\Main\Database\Connection
     */
    public function getConnection() {
        $this->connect();

        return $this->_connection;
    }

    /**
     * Gets instance for SQL select statement.
     *
     * @return \PF\Main\Database\Select
     */
    public function select() {
        $this->connect();

        return new Database\Select($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL insert statement.
     *
     * @return \PF\Main\Database\Insert
     */
    public function insert() {
        $this->connect();

        return new Database\Insert($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL update statement.
     *
     * @return \PF\Main\Database\Update
     */
    public function update() {
        $this->connect();

        return new Database\Update($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL delete statement.
     *
     * @return \PF\Main\Database\Delete
     */
    public function delete() {
        $this->connect();

        return new Database\Delete($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL query statement. Please exact function such as select, insert, update, delete.
     *
     * @return \PF\Main\Database\Query
     */
    public function query() {
        $this->connect();

        return new Database\Query($this->_connection, $this->_logger);
    }

    /**
     * Returns singleton instance of database transaction.
     *
     * @return \PF\Main\Databasse\Transaction
     */
    public function getTransaction() {
        if ($this->_transaction === null) {
            $this->connect();
            $this->_transaction = new Database\Transaction($this->_connection);
        }

        return $this->_transaction;
    }

    /**
     * Method for installing database with all tables.
     *
     * @return \PF\Main\Database
     */
    private function _install() {
        $options = array(
                Database\Connection::ATTR_ERRMODE            => Database\Connection::ERRMODE_EXCEPTION,
                Database\Connection::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET UTF8; SET NAMES UTF8"
            );
        $connection = new Database\Connection($this->_address, $this->_user, $this->_password, null, $options);

        // TODO create install UC
        $connection->exec("CREATE DATABASE IF NOT EXISTS `".$this->_database."` CHARACTER SET utf8 COLLATE=utf8_general_ci");
        $connection->exec("USE ".$this->_database);

        $translateFile = dirname(__DIR__).'/install.sql';
        if (file_exists($translateFile)) {
            $sql = explode(";\n", file_get_contents($translateFile));
            foreach ($sql as $query) {
                if (!empty($query)) {
                    $connection->exec($query);
                }
            }

            rename($translateFile, dirname(__DIR__).'/installed.sql');
        }


        return $this;
    }
}
