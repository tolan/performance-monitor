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
        mysql_query("USE ".$this->_database, $this->_connection);

        mysql_query("CREATE TABLE IF NOT EXISTS `measure` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name`        VARCHAR(64) NULL,
                `description` TEXT NULL,
                `edited`      DATETIME NULL
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `measure_request` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `measureId` INT UNSIGNED NOT NULL,
                `url`       VARCHAR(255) NULL,
                `method`    VARCHAR(16) NULL,
                `toMeasure` TINYINT(1) NULL,
                FOREIGN KEY (`measureId`) REFERENCES `measure` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `request_parameter` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `requestId` INT UNSIGNED NOT NULL,
                `method`    VARCHAR(16) NOT NULL,
                `name`      VARCHAR(64) NOT NULL,
                `value`     VARCHAR(255) NOT NULL,
                FOREIGN KEY (`requestId`) REFERENCES `measure_request` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `measure_test` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `measureId` INT UNSIGNED NOT NULL,
                `state`     VARCHAR(32) NULL,
                `started`   DATETIME NULL,
                FOREIGN KEY (`measureId`) REFERENCES `measure` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `test_attempt` (
                `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `testId`           INT UNSIGNED NOT NULL,
                `url`              VARCHAR(255) NULL,
                `parameters`       TEXT NULL,
                `body`             TEXT NULL,
                `state`            VARCHAR(32) NULL,
                `started`          DATETIME NULL,
                `compensationTime` FLOAT NOT NULL DEFAULT 0,
                `time`             FLOAT NOT NULL,
                `calls`            INT NOT NULL,
                FOREIGN KEY (`testId`) REFERENCES `measure_test` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `attempt_data` (
                `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `attemptId` INT UNSIGNED NOT NULL,
                `file`      VARCHAR(512) NOT NULL,
                `line`      INT NOT NULL,
                `immersion` INT NOT NULL,
                `start`     FLOAT NOT NULL,
                `end`       FLOAT NOT NULL,
                FOREIGN KEY (`attemptId`) REFERENCES `test_attempt` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        mysql_query("CREATE TABLE IF NOT EXISTS `attempt_statistic_data` (
                `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `attemptId` INT UNSIGNED NOT NULL,
                `parentId`  BIGINT UNSIGNED NOT NULL,
                `file`      VARCHAR(512) NOT NULL,
                `line`      INT NOT NULL,
                `content`   TEXT NOT NULL,
                `time`      FLOAT UNSIGNED NOT NULL,
                FOREIGN KEY (`attemptId`) REFERENCES `test_attempt` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'",
            $this->_connection
        );

        $translateFile = dirname(__DIR__).'/translate.sql';
        $sql           = explode(";\n", file_get_contents($translateFile));
        foreach ($sql as $query) {
            if (!empty($query)) {
                mysql_query($query, $this->_connection);
            }
        }

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
    public static function convertTimeToMySQLDateTime($time = null) {
        if (!$time) {
            $time = time();
        }

        return date(self::MYSQL_DATETIME, $time);
    }
}
