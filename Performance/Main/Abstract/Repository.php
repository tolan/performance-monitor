<?php

/**
 * Abstract class for operation with database.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Abstract_Repository {

    /**
     * Database instance
     *
     * @var Performance_Main_Database
     */
    private $_database;

    /**
     * Name of table which is managed by repositor
     *
     * @var string
     */
    private $_tableName = null;

    /**
     * Construct method.
     *
     * @param Performance_Main_Database $database Database instance
     *
     * @return Performance_Main_Abstract_Repository
     */
    final public function __construct(Performance_Main_Database $database) {
        $this->_database = $database;
        $this->init();
    }

    /**
     * Optional init function for set table name and some things.
     *
     * @param string $table Name of table
     *
     * @return Performance_Main_Abstract_Repository
     */
    protected function init($table = null) {
        $this->_tableName = $table;

        return $this;
    }

    /**
     * Returns database instance.
     *
     * @return Performance_Main_Database
     */
    protected function getDatabase() {
        return $this->_database;
    }

    /**
     * Function for delete item by id in database.
     *
     * @param int $id Id of item
     *
     * @return int Count of affected rows
     */
    protected function delete($id) {
        $this->_checkSetTable();

        return $this->getDatabase()
            ->delete()
            ->setTable($this->getTableName())
            ->where('id = ?', $id)
            ->run();
    }

    /**
     * Function for create item with data in database.
     *
     * @param array $data Data which will be inserted.
     *
     * @return int Inserted ID
     */
    protected function create($data) {
        $this->_checkSetTable();

        return $this->getDatabase()
            ->insert()
            ->setTable($this->getTableName())
            ->setInsertData($data)
            ->run();
    }

    /**
     * Function for update item with data by id in database.
     *
     * @param int   $id   Id of item
     * @param array $data Data for update
     *
     * @return int Count of affected rows
     */
    protected function update($id, $data) {
        $this->_checkSetTable();

        return $this->getDatabase()
            ->update()
            ->setTable($this->getTableName())
            ->setUpdateData($data)
            ->where('id = ?', $id)
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
     * @return Performance_Main_Abstract_Repository
     *
     * @throws Performance_Main_Database_Exception Throws when name is not set.
     */
    private function _checkSetTable() {
        if (!$this->_tableName) {
            throw new Performance_Main_Database_Exception('Table name is not defined');
        }

        return $this;
    }
}