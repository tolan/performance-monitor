<?php

namespace PM\scripts\Install\migrations;

use PM\scripts\Install\AbstractMigration;
use PM\Main\Database\Connection;

/**
 * This script defines migration class for initialize datbase.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
class Init_201401010000 extends AbstractMigration {

    private $_configParams = array(
        'address',
        'user',
        'password',
        'database'
    );

    /**
     * It runs migration.
     *
     * @return void
     *
     * @throws Database\Exception Throws when missed at least one of required parameter for connection to database.
     */
    public function run() {
        $configuration = $this->getConfig('database');

        if (count(array_diff($this->_configParams, array_keys($configuration))) > 0) {
            throw new Database\Exception('Wrong configuration. Requested options: '.join(', ', $this->_configParams));
        }

        $this->_install($configuration['address'], $configuration['user'], $configuration['password'], $configuration['database']);
    }

    /**
     * Install method.
     *
     * @param string $address  Address of MySQL database
     * @param string $user     User with access to database
     * @param string $password Password
     * @param string $database Name of database
     *
     * @return void
     */
    private function _install($address, $user, $password, $database) {
        $options = array(
            Connection::ATTR_ERRMODE            => Connection::ERRMODE_EXCEPTION,
            Connection::MYSQL_ATTR_INIT_COMMAND => "SET CHARACTER SET UTF8; SET NAMES UTF8"
        );
        $connection = new Connection($address, $user, $password, null, $options);
        $connection->prepare('')->closeCursor();

        $connection->exec("CREATE DATABASE IF NOT EXISTS `".$database."` CHARACTER SET utf8 COLLATE=utf8_general_ci");
        $connection->exec("USE ".$database);

        $dataFile = __DIR__.'/data/201401010000_Init.sql';
        if (file_exists($dataFile)) {
            $sql = explode(";\n", file_get_contents($dataFile));
            foreach ($sql as $query) {
                if (!empty($query)) {
                    $connection->exec($query);
                }
            }
        }
    }
}
