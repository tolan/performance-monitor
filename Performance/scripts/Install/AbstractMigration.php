<?php

namespace PM\scripts\Install;

use PM\Main\Provider;
use PM\Main\Database\Connection;

/**
 * Abstract class for migrations.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
abstract class AbstractMigration {

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param \PM\Main\Provider $provider Provider instance
     *
     * @return void
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Returns provider instance.
     *
     * @return Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Returns database instance.
     *
     * @return \PM\Main\Database
     */
    final protected function getDatabase() {
        return $this->_provider->get('PM\Main\Database');
    }

    /**
     * Returns utils instance.
     *
     * @return \PM\Main\Utils
     */
    final protected function getUtils() {
        return $this->_provider->get('PM\Main\Utils');
    }

    /**
     * Returns configuration for module.
     *
     * @param string $module Name of config module.
     *
     * @return mixed
     */
    final protected function getConfig($module) {
        return $this->_provider->get('PM\Main\Config')->get($module, array());
    }

    /**
     * Returns connection to the database by configuration.
     *
     * @param string $address  Address of MySQL database
     * @param string $user     User with access to database
     * @param string $password Password
     * @param string $database Name of database
     *
     * @return Connection
     */
    final protected function getConnection($address = null, $user = null, $password = null, $database = null) {
        $configuration = $this->getConfig('database');
        $address       = $address  ? $address  : $configuration['address'];
        $user          = $user     ? $user     : $configuration['user'];
        $password      = $password ? $password : $configuration['password'];
        $database      = $database ? $database : $configuration['database'];

        $options = array(
            Connection::ATTR_ERRMODE            => Connection::ERRMODE_EXCEPTION,
            Connection::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET UTF8; SET NAMES UTF8"
        );
        $connection = new Connection($address, $user, $password, $database, $options);
        $connection->prepare('')->closeCursor();

        return $connection;
    }

    /**
     * It load sql file and execute it.
     *
     * @param string     $filename   Path to the file with sql statements
     * @param Connection $connection Connection to the database
     *
     * @return AbstractMigration
     */
    final protected function loadSQLFile($filename, Connection $connection=null) {
        $connection = $connection ? $connection : $this->getConnection();

        if (file_exists($filename)) {
            $sql = explode(";\n", file_get_contents($filename));
            foreach ($sql as $query) {
                if (!empty($query)) {
                    $connection->exec($query);
                }
            }
        }

        return $this;
    }

    /**
     * Abstract method for start migration.
     */
    abstract public function run();
}
