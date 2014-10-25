<?php

namespace PM\Main;

use PM\Main\Log;
use PM\scripts\Install;

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
     * @var \PM\Main\Database\Transaction
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
     * @var \PM\Main\Database\Connection
     */
    private $_connection;

    /**
     * Logger instance.
     *
     * @var \PM\Main\Log
     */
    private $_logger;

    /**
     * Construct method which sets parameters to database connection.
     *
     * @param \PM\Main\Config   $config   Configuration
     * @param \PM\Main\Log      $logger   Logger instance
     * @param \PM\Main\Provider $provider Provider instance (needed for installation)
     *
     * @throws \PM\Main\Database\Exception Throws when missing some configuration option
     */
    public function __construct(Config $config, Log $logger, Provider $provider) {
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
            $provider->set($this);
            Install\Manager::run($provider);
        }
    }

    /**
     * This method provides connection to database.
     *
     * @return \PM\Main\Database
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
     * This method disconnect connection to database.
     *
     * @return \PM\Main\Database
     */
    public function disconnect() {
        if ($this->_isConnected === true) {
            $this->_connection = null;
            $this->_isConnected = false;
        }

        return $this;
    }

    /**
     * Returns PDO connection to database. It is for create custom statements.
     *
     * @return \PM\Main\Database\Connection
     */
    public function getConnection() {
        $this->connect();

        return $this->_connection;
    }

    /**
     * Gets instance for SQL select statement.
     *
     * @return \PM\Main\Database\Select
     */
    public function select() {
        $this->connect();

        return new Database\Select($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL insert statement.
     *
     * @return \PM\Main\Database\Insert
     */
    public function insert() {
        $this->connect();

        return new Database\Insert($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL update statement.
     *
     * @return \PM\Main\Database\Update
     */
    public function update() {
        $this->connect();

        return new Database\Update($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL delete statement.
     *
     * @return \PM\Main\Database\Delete
     */
    public function delete() {
        $this->connect();

        return new Database\Delete($this->_connection, $this->_logger);
    }

    /**
     * Gets instance for SQL query statement. Please exact function such as select, insert, update, delete.
     *
     * @return \PM\Main\Database\Query
     */
    public function query() {
        $this->connect();

        return new Database\Query($this->_connection, $this->_logger);
    }

    /**
     * Returns singleton instance of database transaction.
     *
     * @return \PM\Main\Databasse\Transaction
     */
    public function getTransaction() {
        if ($this->_transaction === null) {
            $this->connect();
            $this->_transaction = new Database\Transaction($this->_connection);
        }

        return $this->_transaction;
    }
}
