<?php

namespace PM\Main\Database;

/**
 * This script defines class for update statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method \PM\Main\Database\Update where(string $condition, array $bind=null)   It adds condition with AND operator.
 * @method \PM\Main\Database\Update orWhere(string $condition, array $bind=null) It adds condition with OR operator.
 * @method \PM\Main\Database\Update setSQL(string $sql)                          It sets SQL qeury.
 */
class Update extends Where {

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
     * @return \PM\Main\Database\Update
     */
    public function setTable($table) {
        $alias = is_array($table) ? key($table) : $table;
        $table = is_array($table) ? current($table) : $table;

        $this->_table = array(
            'table' => $table,
            'alias' => $alias
        );

        return $this;
    }

    /**
     * This method sets data for update in format array(column => data).
     *
     * @param array $data Data to update
     *
     * @return \PM\Main\Database\Update
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
     * @return \PM\Main\Database\Update
     *
     * @throws \PM\Main\Database\Exception Throws when table or data are not set.
     */
    protected function compile() {
        if ($this->_table === null) {
            throw new Exception('Table is not set.');
        }

        if (empty($this->_data)) {
            throw new Exception('Data are not set.');
        }

        $sql          = 'UPDATE '.$this->_table['table'].' AS '.$this->_table['alias']. ' SET ';
        $placeholders = array();
        $bind         = array();

        foreach ($this->_data as $column => $data) {
            $columnName        = strpos($column, '.') ? $column : ($this->_table['alias'].'.'.$column);
            $placeholders[]    = $columnName.' = :'.$column;
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
