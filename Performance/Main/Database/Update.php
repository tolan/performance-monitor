<?php

/**
 * This script defines class for update statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method Performance_Main_Database_Select where(string $condition, array $bind=null) It adds condition with AND operator.
 * @method Performance_Main_Database_Select orWhere(string $condition, array $bind=null) It adds condition with OR operator.
 * @method Performance_Main_Database_Select setSQL(string $sql) It adds condition with OR operator.
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

        return $this->execute($this->getStatement(), $this->getBind());
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

        $sql          = 'UPDATE '.$this->_table. ' SET ';
        $placeholders = array();
        $bind         = array();

        foreach ($this->_data as $column => $data) {
            $placeholders[] = $column.' = :'.$column;
            $bind[':'.$column] = $data;
        }

        $sql .= join(', ', $placeholders);

        $where = parent::compile();
        $sql  .= $where == '' ? '' : ' WHERE '.$where;

        $this->setStatement($sql);
        $this->setBind($bind);

        return $this;
    }
}
