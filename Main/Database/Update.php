<?php

/**
 * This script defines class for update statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Database_Update extends Performance_Main_Database_Where {

    /**
     * Name of table
     *
     * @var string
     */
    private $_table = null;

    /**
     * Data for insert
     *
     * @var array
     */
    private $_data = array();

    /**
     * Method for set table.
     *
     * @param string $table Name of table
     *
     * @return Performance_Main_Database_Update
     */
    public function setTable($table) {
        $this->_table = is_array($table) ? current($table) : $table;

        return $this;
    }

    /**
     * This method sets data for update in format array(column => data).
     *
     * @param array $data Data to update
     *
     * @return Performance_Main_Database_Update
     */
    public function setUpdateData($data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * This runs SQL update stament and returns count of affected rows.
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
     * @return Performance_Main_Database_Update
     *
     * @throws Performance_Main_Database_Exception Throws when table or data are not set.
     */
    protected function compile() {
        if ($this->_table === null) {
            throw new Performance_Main_Database_Exception('Table is not set.');
        }

        if (empty($this->_data)) {
            throw new Performance_Main_Database_Exception('Data are not set.');
        }

        $sql = 'UPDATE '.$this->_table. ' SET ';
        $updates = array();

        foreach ($this->_data as $column => $data) {
            $updates[] = $column.' = '.$this->cleanData($data);
        }

        $sql .= join(', ', $updates);

        $where = parent::compile();
        $sql .= $where == '' ? '' : ' WHERE '.$where;

        $this->sql = $sql;

        return $this;
    }
}
