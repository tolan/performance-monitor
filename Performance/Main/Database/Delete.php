<?php

/**
 * This script defines class for delete statement of MySQL.
 * ATTENTION: This can delete whole table.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Database_Delete extends Performance_Main_Database_Where {

    /**
     * Name of table
     *
     * @var string
     */
    private $_table = null;

    /**
     * Method for set table.
     *
     * @param string $table Name of table
     *
     * @return Performance_Main_Database_Delete
     */
    public function setTable($table) {
        $this->_table = is_array($table) ? current($table) : $table;

        return $this;
    }

    /**
     * This runs SQL delete stament and returns count of affected rows.
     * ATTENTION: This can delete whole table.
     *
     * @return int Count of affected rows.
     */
    public function run() {
        $this->preFetch();

        $this->fetch($this->sql);

        return mysql_affected_rows($this->_connection);
    }

    /**
     * This create SQL statement from input data.
     *
     * @return Performance_Main_Database_Delete
     *
     * @throws Performance_Main_Database_Exception Throws when table is not set.
     */
    protected function compile() {
        if ($this->_table === null) {
            throw new Performance_Main_Database_Exception('Table is not set.');
        }

        $sql = 'DELETE FROM '.$this->_table;

        $where = parent::compile();
        $sql .= $where == '' ? '' : ' WHERE '.$where;

        $this->sql = $sql;

        return $this;
    }
}
