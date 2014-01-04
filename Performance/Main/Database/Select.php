<?php

/**
 * This script defines class for select statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method Performance_Main_Database_Select where(string $condition, array $bind=null) It adds condition with AND operator.
 * @method Performance_Main_Database_Select orWhere(string $condition, array $bind=null) It adds condition with OR operator.
 * @method Performance_Main_Database_Select setSQL(string $sql) It adds condition with OR operator.
 */
class Performance_Main_Database_Select extends Performance_Main_Database_Where {

    /**
     * Parameter for DISTINCT function.
     *
     * @var type
     */
    private $_distinct = '';

    /**
     * List of columns which will be returned.
     *
     * @var array
     */
    private $_columns = array();

    /**
     * Name of table which is used for FROM part
     *
     * @var array
     */
    private $_table = null;

    /**
     * List of joined tables
     *
     * @var array
     */
    private $_joins = array();

    /**
     * GROUP part of statement
     *
     * @var string
     */
    private $_group = null;

    /**
     * HAVING part of statement
     *
     * @var string
     */
    private $_having = null;

    /**
     * It adds DISTINCT to SQL statement.
     *
     * @return Performance_Main_Database_Select
     */
    public function distinct() {
        $this->_distinct = ' DISTINCT';

        return $this;
    }

    /**
     * Returns new instance of where condition.
     *
     * @return \Performance_Main_Database_Where
     */
    public function createWhere() {
        return new parent($this->getConnection());
    }

    /**
     * Sets columns which will be selected. It is good for special formating and functions.
     *
     * @param array $columns Array with selected columns
     *
     * @return Performance_Main_Database_Select
     */
    public function columns($columns) {
        if (isset($this->_columns[''])) {
            $columns = array_merge($this->_columns[''], (array)$columns);
        }

        $this->_columns[''] = (array)$columns;

        return $this;
    }

    /**
     * It sets part FROM.
     *
     * @param string|array $table   Name of table or alias => table array
     * @param array        $columns List of columns wich will be returned
     *
     * @return Performance_Main_Database_Select
     */
    public function from($table, $columns=array('*')) {
        $alias = is_array($table) ? key($table) : $table;
        $table = is_array($table) ? current($table) : $table;

        $this->_table = array(
            'table' => $table,
            'alias' => $alias
        );

        $this->_columns[$alias] = (array)$columns;

        return $this;
    }

    /**
     * It adds INNER JOIN function to SQL statement.
     *
     * @param string|array $table   Name of table or alias => table array
     * @param string       $on      ON condition to connection of tables
     * @param array        $columns List of columns wich will be returned
     *
     * @return Performance_Main_Database_Select
     */
    public function joinInner($table, $on, $columns=array('*')) {
        return $this->_join($table, $on, $columns, 'INNER JOIN');
    }

    /**
     * It adds LEFT JOIN function to SQL statement.
     *
     * @param string|array $table   Name of table or alias => table array
     * @param string       $on      ON condition to connection of tables
     * @param array        $columns List of columns wich will be returned
     *
     * @return Performance_Main_Database_Select
     */
    public function joinLeft($table, $on, $columns=array('*')) {
        return $this->_join($table, $on, $columns, 'LEFT JOIN');
    }

    /**
     * It adds GROUP BY function to SQL statement.
     *
     * @param array $columns List of columns for group function
     *
     * @return Performance_Main_Database_Select
     */
    public function group($columns = null) {
        $this->_group = join((', '), (array)$columns);

        return $this;
    }

    /**
     * It adds HAVING function to SQL statement.
     *
     * @param string|Performance_Main_Database_Where $condition HAVING condition
     *
     * @return Performance_Main_Database_Select
     */
    public function having($condition = null) {
        $this->_having = (string)$condition;

        return $this;
    }

    /**
     * It creates SQL select statement.
     *
     * @return Performance_Main_Database_Select
     *
     * @throws Performance_Main_Database_Exception Throws when FROM table is not set.
     */
    protected function compile() {
        if ($this->_table === null) {
            throw new Performance_Main_Database_Exception('From table is not set.');
        }

        $sql = 'SELECT'.$this->_distinct;

        if (empty($this->_columns)) {
            $sql .= ' *';
        } else {
            $cols = array();
            foreach ($this->_columns as $table => $columns) {
                $table = empty($table) ? '' : $table.'.';
                foreach ($columns as $alias => $column) {
                    if (is_numeric($alias)) {
                        $cols[] = $table.$column;
                    } else {
                        $cols[] = $table.$column.' AS '.$alias;
                    }
                }
            }

            $sql .= ' '.join(', ', $cols);
        }

        $sql .= ' FROM '.$this->_table['table'].' AS '.$this->_table['alias'];

        $joins = array();
        foreach ($this->_joins as $alias => $join) {
            $joins[] = $join['type'].' '.$join['table'].' AS '.$alias.' ON '.$join['on'];
        }

        $sql .= !empty($joins) ? ' '.join(' ', $joins) : '';

        $where = parent::compile();
        $sql .= $where == '' ? '' : ' WHERE '.$where;

        $sql .= $this->_group  === null ? '' : ' GROUP BY '.$this->_group;
        $sql .= $this->_having === null ? '' : ' HAVING '.$this->_having;

        $this->setStatement($sql);

        return $this;
    }

    /**
     * It adds JOIN function to SQL statement. It is entry point for both join function.
     *
     * @param string|array $table    Name of table or alias => table array
     * @param string       $on       ON condition to connection of tables
     * @param array        $columns  List of columns wich will be returned
     * @param string       $joinType Define JOIN type (INNER or LEFT)
     *
     * @return Performance_Main_Database_Select
     */
    private function _join($table, $on, $columns=array('*'), $joinType = 'INNER JOIN') {
        $alias = is_array($table) ? key($table) : $table;
        $table = is_array($table) ? current($table) : $table;

        $this->_joins[$alias] = array(
            'type' => $joinType,
            'on' => $on,
            'table' => $table
        );

        $this->_columns[$alias] = (array)$columns;

        return $this;
    }
}
