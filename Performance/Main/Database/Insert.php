<?php

namespace PM\Main\Database;

/**
 * This script defines class for insert statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Insert extends Query {

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
     * Data for mass insert.
     *
     * @var array
     */
    private $_massData = array();

    /**
     * Last insert id
     *
     * @var int
     */
    private $_insertId = null;

    /**
     * Method for set table.
     *
     * @param string $table Name of table
     *
     * @return \PM\Main\Database\Insert
     */
    public function setTable($table) {
        $this->_table = is_array($table) ? current($table) : $table;

        return $this;
    }

    /**
     * This method sets data for insert in format array(column => data).
     *
     * @param array $data Data to insert
     *
     * @return \PM\Main\Database\Insert
     */
    public function setInsertData(array $data) {
        $this->_data = $data;

        return $this;
    }

    /**
     * This method provides insert multiple rows in one insert statement.
     *
     * @param array $data Data for mass insert (multiple rows)
     *
     * @return \PM\Main\Database\Insert
     */
    public function massInsert(array $data) {
        $this->_massData = $data;

        return $this;
    }

    /**
     * This runs SQL insert stament and returns last inserted id.
     *
     * @return int Last insert ID.
     */
    public function run() {
        $this->preFetch();

        $this->execute($this->getStatement(), $this->getBind());

        $id = $this->getConnection()->lastInsertId();

        $this->_insertId = $id;

        return $id;
    }

    /**
     * Return last insert ID.
     *
     * @return int Last insert ID
     *
     * @throws \PM\Main\Database\Exception Throws when insert ID is not available.
     */
    public function getId() {
        if ($this->_insertId === null) {
            throw new Exception('Insert ID is not available.');
        }

        return $this->_insertId;
    }

    /**
     * This create SQL statement from input data.
     *
     * @return \PM\Main\Database\Insert
     *
     * @throws \PM\Main\Database\Exception Throws when table or data are not set.
     */
    protected function compile() {
        if ($this->_table === null) {
            throw new Exception('Table is not set.');
        }

        if (empty($this->_data) && empty($this->_massData)) {
            throw new Exception('Data are not set.');
        }

        $data  = array_filter(array_merge($this->_massData, array($this->_data)));
        $first = $data[current(array_keys($data))];

        $sql = 'INSERT INTO '.$this->_table;

        $columns = '(`'.join('`, `', array_keys($first)).'`)';

        $placeholders = array();
        $bind = array();
        foreach ($data as $row) {
            $placeholders[] = '('.rtrim(str_repeat('?, ', count($row)), ', ').')';
            foreach ($row as $field) {
                $bind[] = $field;
            }
        }

        $values = join(', ', $placeholders);
        $sql .= ' '.$columns.' VALUES '.$values;

        $this->setStatement($sql);
        $this->setBind($bind);

        return $this;
    }
}
