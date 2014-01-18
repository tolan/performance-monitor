<?php

namespace PF\Main;

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
     * Construct method which sets parameters to database connection.
     *
     * @param \PF\Main\Config $config Configuration
     *
     * @throws \PF\Main\Database\Exception Throws when missing some configuration option
     */
    public function __construct(Config $config) {
        $configuration = $config->get('database');

        if (count(array_diff($this->_configParams, array_keys($configuration))) > 0) {
            throw new Database\Exception('Wrong configuration. Requested options: '.join(', ', $this->_configParams));
        }

        $this->_address  = $configuration['address'];
        $this->_user     = $configuration['user'];
        $this->_password = $configuration['password'];
        $this->_database = $configuration['database'];

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

        return new Database\Select($this->_connection);
    }

    /**
     * Gets instance for SQL insert statement.
     *
     * @return \PF\Main\Database\Insert
     */
    public function insert() {
        $this->connect();

        return new Database\Insert($this->_connection);
    }

    /**
     * Gets instance for SQL update statement.
     *
     * @return \PF\Main\Database\Update
     */
    public function update() {
        $this->connect();

        return new Database\Update($this->_connection);
    }

    /**
     * Gets instance for SQL delete statement.
     *
     * @return \PF\Main\Database\Delete
     */
    public function delete() {
        $this->connect();

        return new Database\Delete($this->_connection);
    }

    /**
     * Gets instance for SQL query statement. Please exact function such as select, insert, update, delete.
     *
     * @return \PF\Main\Database\Query
     */
    public function query() {
        $this->connect();

        return new Database\Query($this->_connection);
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

        $connection->exec("CREATE DATABASE IF NOT EXISTS `".$this->_database."` CHARACTER SET utf8 COLLATE=utf8_general_ci");
        $connection->exec("USE ".$this->_database);

        $connection->exec("CREATE TABLE IF NOT EXISTS `measure` (
                `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `name`        VARCHAR(64) NULL,
                `description` TEXT NULL,
                `edited`      DATETIME NULL
            ) ENGINE='InnoDB'"
        );

        $connection->exec("CREATE TABLE IF NOT EXISTS `measure_request` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `measureId` INT UNSIGNED NOT NULL,
                `url`       VARCHAR(255) NULL,
                `method`    VARCHAR(16) NULL,
                `toMeasure` TINYINT(1) NULL,
                FOREIGN KEY (`measureId`) REFERENCES `measure` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'"
        );

        $connection->exec("CREATE TABLE IF NOT EXISTS `request_parameter` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `requestId` INT UNSIGNED NOT NULL,
                `method`    VARCHAR(16) NOT NULL,
                `name`      VARCHAR(64) NOT NULL,
                `value`     VARCHAR(255) NOT NULL,
                FOREIGN KEY (`requestId`) REFERENCES `measure_request` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'"
        );

        $connection->exec("CREATE TABLE IF NOT EXISTS `measure_test` (
                `id`        INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `measureId` INT UNSIGNED NOT NULL,
                `state`     VARCHAR(32) NULL,
                `started`   DATETIME NULL,
                FOREIGN KEY (`measureId`) REFERENCES `measure` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'"
        );

        $connection->exec("CREATE TABLE IF NOT EXISTS `test_attempt` (
                `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `testId`           INT UNSIGNED NOT NULL,
                `url`              VARCHAR(255) NULL,
                `method`           VARCHAR(16) NOT NULL,
                `parameters`       TEXT NULL,
                `body`             TEXT NULL,
                `state`            VARCHAR(32) NULL,
                `started`          DATETIME NULL,
                `compensationTime` FLOAT NOT NULL DEFAULT 0,
                `time`             FLOAT NOT NULL,
                `calls`            INT NOT NULL,
                FOREIGN KEY (`testId`) REFERENCES `measure_test` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'"
        );

        $connection->exec("CREATE TABLE IF NOT EXISTS `attempt_data` (
                `id`        BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `attemptId` INT UNSIGNED NOT NULL,
                `file`      VARCHAR(512) NOT NULL,
                `line`      INT NOT NULL,
                `immersion` INT NOT NULL,
                `start`     FLOAT NOT NULL,
                `end`       FLOAT NOT NULL,
                FOREIGN KEY (`attemptId`) REFERENCES `test_attempt` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'"
        );

        $connection->exec("CREATE TABLE IF NOT EXISTS `attempt_statistic_data` (
                `id`           BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `attemptId`    INT UNSIGNED NOT NULL,
                `parentId`     BIGINT UNSIGNED NOT NULL,
                `file`         VARCHAR(512) NOT NULL,
                `line`         INT NOT NULL,
                `content`      TEXT NOT NULL,
                `time`         FLOAT UNSIGNED NOT NULL,
                `timeSubStack` FLOAT UNSIGNED NOT NULL,
                FOREIGN KEY (`attemptId`) REFERENCES `test_attempt` (`id`) ON DELETE CASCADE
            ) ENGINE='InnoDB'"
        );

        $translateFile = dirname(__DIR__).'/translate.sql';
        if (file_exists($translateFile)) {
            $sql = explode(";\n", file_get_contents($translateFile));
            foreach ($sql as $query) {
                if (!empty($query)) {
                    $connection->exec($query);
                }
            }

            rename($translateFile, dirname(__DIR__).'/translateLoaded.sql');
        }


        return $this;
    }
}
