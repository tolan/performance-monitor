<?php

/**
 * This script defines class for connect to MySQL database and provide basic function.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Database {
    const MYSQL_DATETIME = 'Y-m-d H:i:s';

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
     *Connection to database.
     *
     * @var resource
     */
    private $_connection;

    /**
     * Construct method which sets parameters to database connection.
     *
     * @param Performance_Main_Config $config Configuration
     *
     * @throws Performance_Main_Database_Exception Throws when missing some configuration option
     */
    public function __construct(Performance_Main_Config $config) {
        $configuration = $config->get('database');

        if (count(array_diff($this->_configParams, array_keys($configuration))) > 0) {
            throw new Performance_Main_Database_Exception('Wrong configuration. Requested options: '.join(', ', $this->_configParams));
        }

        $this->_address  = $configuration['address'];
        $this->_user     = $configuration['user'];
        $this->_password = $configuration['password'];
        $this->_database = $configuration['database'];

        if ($configuration['install'] === true) {
            $this->connect();
            $this->_install();
        }
    }

    /**
     * This method provides connection to database.
     *
     * @return Performance_Main_Database
     */
    public function connect() {
        if ($this->_isConnected === false) {
            $this->_connection = mysql_connect($this->_address, $this->_user, $this->_password, true);
            mysql_query("SET CHARACTER SET utf8");
            mysql_query("SET NAMES utf8");
            mysql_select_db($this->_database);
            $this->_isConnected = true;
        }

        return $this;
    }

    /**
     * Gets instance for SQL select statement.
     *
     * @return Performance_Main_Database_Select
     */
    public function select() {
        $this->connect();

        return new Performance_Main_Database_Select($this->_connection);
    }

    /**
     * Gets instance for SQL insert statement.
     *
     * @return Performance_Main_Database_Insert
     */
    public function insert() {
        $this->connect();

        return new Performance_Main_Database_Insert($this->_connection);
    }

    /**
     * Gets instance for SQL update statement.
     *
     * @return Performance_Main_Database_Update
     */
    public function update() {
        $this->connect();

        return new Performance_Main_Database_Update($this->_connection);
    }

    /**
     * Gets instance for SQL delete statement.
     *
     * @return Performance_Main_Database_Update
     */
    public function delete() {
        $this->connect();

        return new Performance_Main_Database_Delete($this->_connection);
    }

    /**
     * Gets instance for SQL query statement. Please exact function such as select, insert, update, delete.
     *
     * @return Performance_Main_Database_Query
     */
    public function query() {
        $this->connect();

        return new Performance_Main_Database_Query($this->_connection);
    }

    /**
     * Method for installing database with all tables.
     *
     * @return Performance_Main_Database
     */
    private function _install() {
        mysql_query("CREATE DATABASE IF NOT EXISTS `".$this->_database."` CHARACTER SET utf8 COLLATE=utf8_general_ci", $this->_connection);

        mysql_query("CREATE TABLE IF NOT EXISTS `profiler_measure` (
            `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `name` VARCHAR(64) NULL,
            `description` text NULL,
            `link` VARCHAR(255) NULL,
            `edited` DATETIME NULL,
            PRIMARY KEY (`id`)
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `profiler_measure_parameter` (
            `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `profiler_measure_id` int(10) unsigned NOT NULL,
            `key` varchar(64) NOT NULL,
            `value` varchar(255) NOT NULL,
            FOREIGN KEY (`profiler_measure_id`) REFERENCES `profiler_measure` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `profiler_measure_attempt` (
            `id` int(10) unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `profiler_measure_id` int(10) unsigned NOT NULL,
            `state` VARCHAR(32) NULL,
            `started` DATETIME NULL,
            `compensationTime` FLOAT NOT NULL DEFAULT 0,
            FOREIGN KEY (`profiler_measure_id`) REFERENCES `profiler_measure` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `profiler_measure_data` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `profiler_measure_attempt_id` int(10) unsigned NOT NULL,
            `file` varchar(512) NOT NULL,
            `line` int NOT NULL,
            `immersion` int NOT NULL,
            `start` float NOT NULL,
            `end` float NOT NULL,
            FOREIGN KEY (`profiler_measure_attempt_id`) REFERENCES `profiler_measure_attempt` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `profiler_measure_statistic` (
            `id` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `profiler_measure_attempt_id` int(10) unsigned NOT NULL,
            `time` float NOT NULL,
            `calls` int NOT NULL,
            FOREIGN KEY (`profiler_measure_attempt_id`) REFERENCES `profiler_measure_attempt` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `profiler_measure_statistic_data` (
            `id` bigint unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `profiler_measure_statistic_id` int(10) unsigned NOT NULL,
            `parent_id` bigint unsigned NOT NULL,
            `file` varchar(512) NOT NULL,
            `line` int NOT NULL,
            `content` text NOT NULL,
            `time` float unsigned NOT NULL,
            FOREIGN KEY (`profiler_measure_statistic_id`) REFERENCES `profiler_measure_statistic` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        return $this;
    }

    /**
     * Destruct method close connection to MYSQL database.
     *
     * @return void
     */
    public function __destruct() {
        $this->disconnect();
    }

    /**
     * Method for disconnect connection to MYSQL database.
     *
     * @return Performance_Main_Database
     */
    public function disconnect() {
        if ($this->_isConnected === true) {
            mysql_close($this->_connection);
            $this->_isConnected = false;
        }

        return $this;
    }

    /**
     * Helper method for convert time to MYSQL datetime format.
     *
     * @param int $time Time in seconds
     *
     * @return string
     */
    public static function convertTimeToMySQLDateTime($time) {
        return date(self::MYSQL_DATETIME, $time);
    }
}
