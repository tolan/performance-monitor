<?php

namespace PF\Main\Abstracts;

use PF\Main\Database;
use PF\Main\Database\Exception;
use PF\Main\Utils;

/**
 * Abstract class for operation with database.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Repository {

    /**
     * Database instance
     *
     * @var \PF\Main\Database
     */
    private $_database;

    /**
     * Utils instance.
     *
     * @var \PF\Main\Utils
     */
    private $_utils;

    /**
     * Name of table which is managed by repositor
     *
     * @var string
     */
    private $_tableName = null;

    /**
     * Construct method.
     *
     * @param \PF\Main\Database $database Database instance
     */
    final public function __construct(Database $database, Utils $utils) {
        $this->_database = $database;
        $this->_utils    = $utils;
        $this->init();
    }

    /**
     * Optional init function for set table name and some things.
     *
     * @param string $table Name of table
     *
     * @return \PF\Main\Abstracts\Repository
     */
    protected function init($table = null) {
        $this->_tableName = $table;

        return $this;
    }

    /**
     * Returns database instance.
     *
     * @return \PF\Main\Database
     */
    protected function getDatabase() {
        return $this->_database;
    }

    /**
     * Returns utils instance.
     *
     * @return \PF\Main\Utils
     */
    protected function getUtils() {
        return $this->_utils;
    }

    /**
     * Function for delete item by id in database.
     *
     * @param int    $id        Id of item
     * @param string $tableName Name of table [optional]
     *
     * @return int Count of affected rows
     */
    protected function delete($id, $tableName=null) {
        $this->_checkSetTable();

        $tableName = $tableName === null ? $this->getTableName() : $tableName;

        return $this->getDatabase()
            ->delete()
            ->setTable($tableName)
            ->where('id = :id', array(':id' => $id))
            ->run();
    }

    /**
     * Function for create item with data in database.
     *
     * @param array  $data      Data which will be inserted.
     * @param string $tableName Name of table [optional]
     *
     * @return int Inserted ID
     */
    protected function create($data, $tableName=null) {
        $this->_checkSetTable();

        $tableName = $tableName === null ? $this->getTableName() : $tableName;

        return $this->getDatabase()
            ->insert()
            ->setTable($tableName)
            ->setInsertData($data)
            ->run();
    }

    /**
     * Function for update item with data by id in database.
     *
     * @param int    $id        Id of item
     * @param array  $data      Data for update
     * @param string $tableName Name of table [optional]
     *
     * @return int Count of affected rows
     */
    protected function update($id, $data, $tableName=null) {
        $this->_checkSetTable();

        $tableName = $tableName === null ? $this->getTableName() : $tableName;

        return $this->getDatabase()
            ->update()
            ->setTable($tableName)
            ->setUpdateData($data)
            ->where('id = :id', array(':id' => $id))
            ->run();
    }

    /**
     * Returns name of table.
     *
     * @return string
     */
    protected function getTableName() {
        return $this->_tableName;
    }

    /**
     * Checks that table name is set.
     *
     * @return \PF\Main\Abstracts\Repository
     *
     * @throws \PF\Main\Database\Exception Throws when name is not set.
     */
    private function _checkSetTable() {
        if (!$this->_tableName) {
            throw new Exception('Table name is not defined');
        }

        return $this;
    }
}