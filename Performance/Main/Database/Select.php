<?php

namespace PF\Main\Database;

/**
 * This script defines class for select statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method \PF\Main\Database\Select where(string $condition, array $bind=null)   It adds condition with AND operator.
 * @method \PF\Main\Database\Select orWhere(string $condition, array $bind=null) It adds condition with OR operator.
 * @method \PF\Main\Database\Select setSQL(string $sql)                          It sets SQL query.
 */
class Select extends Where {

    const PART_DISTINCT = 'distinct';
    const PART_COLUMNS  = 'columns';
    const PART_FROM     = 'from';
    const PART_JOINS    = 'joins';

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
    private $_from = null;

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
     * ORDER part of statement
     *
     * @var string
     */
    private $_order = null;

    /**
     * LIMIT part of statement
     *
     * @var string
     */
    private $_limit = null;

    /**
     * It adds DISTINCT to SQL statement.
     *
     * @return \PF\Main\Database\Select
     */
    public function distinct() {
        $this->_distinct = ' DISTINCT';

        return $this;
    }

    /**
     * Returns new instance of where condition.
     *
     * @return \PF\Main\Database\Where
     */
    public function createWhere() {
        return new parent($this->getConnection(), $this->getLogger());
    }

    /**
     * Get dynamic part of statement.
     *
     * @param string $part Name of part
     *
     * @return mixed
     */
    public function getPart($part = null) {
        $result = null;

        if ($part === null) {
            $constants = (new \ReflectionClass(get_called_class()))->getConstants();
            $result    = array();
            foreach ($constants as $part) {
                $result[$part] = $this->getPart($part);
            }
        } else {
            switch ($part) {
                case self::PART_COLUMNS:
                    $result = $this->_columns;
                    break;
                case self::PART_DISTINCT:
                    $result = $this->_distinct;
                    break;
                case self::PART_FROM:
                    $result = $this->_from;
                    break;
                case self::PART_JOINS:
                    $result = $this->_joins;
                    break;
                default :
                    $result = parent::getPart($part);
                    break;
            }
        }

        return $result;
    }

    /**
     * Reset dynamic part to default value.
     *
     * @param string $part Name of part
     *
     * @return \PF\Main\Database\Select
     */
    public function resetPart($part = null) {
        if ($part === null) {
            $constants = (new \ReflectionClass(get_called_class()))->getConstants();
            foreach ($constants as $part) {
                $this->resetPart($part);
            }
        } else {
            switch ($part) {
                case self::PART_COLUMNS:
                    $this->_columns = array();
                    break;
                case self::PART_DISTINCT:
                    $this->_distinct = '';
                    break;
                case self::PART_FROM:
                    $this->_from = null;
                    break;
                case self::PART_JOINS:
                    $this->_joins = array();
                    break;
                default :
                    parent::resetPart($part);
                    break;
            }
        }

        return $this;
    }

    /**
     * Sets columns which will be selected. It is good for special formating and functions.
     *
     * @param array $columns Array with selected columns
     *
     * @return \PF\Main\Database\Select
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
     * @return \PF\Main\Database\Select
     */
    public function from($table, $columns = array('*')) {
        $alias = is_array($table) ? key($table) : $table;
        $table = is_array($table) ? current($table) : $table;

        $this->_from = array(
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
     * @return \PF\Main\Database\Select
     */
    public function joinInner($table, $on, $columns = array('*')) {
        return $this->_join($table, $on, $columns, 'INNER JOIN');
    }

    /**
     * It adds LEFT JOIN function to SQL statement.
     *
     * @param string|array $table   Name of table or alias => table array
     * @param string       $on      ON condition to connection of tables
     * @param array        $columns List of columns wich will be returned
     *
     * @return \PF\Main\Database\Select
     */
    public function joinLeft($table, $on, $columns = array('*')) {
        return $this->_join($table, $on, $columns, 'LEFT JOIN');
    }

    /**
     * It adds GROUP BY function to SQL statement.
     *
     * @param array $columns List of columns for group function
     *
     * @return \PF\Main\Database\Select
     */
    public function group($columns = null) {
        $this->_group = join((', '), (array)$columns);

        return $this;
    }

    /**
     * It adds HAVING function to SQL statement.
     *
     * @param string|\PF\Main\Database\Where $condition HAVING condition
     *
     * @return \PF\Main\Database\Select
     */
    public function having($condition = null) {
        $this->_having = (string)$condition;

        return $this;
    }

    /**
     * It adds ORDER function to SQL statement.
     *
     * @param string|array $columns ORDER condition
     *
     * @return \PF\Main\Database\Select
     */
    public function order($columns = array()) {
        if ($columns === null || $columns === false) {
            $this->_order = null;
        }
        if (is_array($columns)) {
            foreach ($columns as $alias => $column) {
                $this->_order[] .= $alias.'.'.$column;
            }
        } else {
            $this->_order[] = (string)$columns;
        }

        $this->_order = array_unique($this->_order);

        return $this;
    }

    /**
     * It adds LIMIT function to SQL statement.
     *
     * @param string $limit LIMIT condition
     *
     * @return \PF\Main\Database\Select
     */
    public function limit($limit='0') {
        if ($limit === '0' || $limit === 0 || $limit === false || $limit === null) {
            $this->_limit = null;
        } else {
            $this->_limit = $limit;
        }

        return $this;
    }

    /**
     * It creates SQL select statement.
     *
     * @return \PF\Main\Database\Select
     *
     * @throws \PF\Main\Database\Exception Throws when FROM table is not set.
     */
    protected function compile() {
        if ($this->_from === null) {
            throw new Exception('From table is not set.');
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

        $from = $this->_from['table'];
        if ($this->_from['table'] instanceof Select) {
            $from = '('.$from.')';
        }

        $sql .= ' FROM '.$from.' AS '.$this->_from['alias'];

        $joins = array();
        foreach ($this->_joins as $alias => $join) {
            if ($join['table'] instanceof Select) {
                $join['table'] = '('.$join['table'].')';
            }

            $joins[] = $join['type'].' '.$join['table'].' AS '.$alias.' ON '.$join['on'];
        }

        $sql .= !empty($joins) ? ' '.join(' ', $joins) : '';

        $where = parent::compile();
        $sql .= $where == '' ? '' : ' WHERE '.$where;

        $sql .= $this->_group  === null ? '' : ' GROUP BY '.$this->_group;
        $sql .= $this->_having === null ? '' : ' HAVING '.$this->_having;
        $sql .= $this->_order  === null ? '' : ' ORDER BY '.trim(join(', ', $this->_order));
        $sql .= $this->_limit  === null ? '' : ' LIMIT '.$this->_limit;

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
     * @return \PF\Main\Database\Select
     */
    private function _join($table, $on, $columns=array('*'), $joinType = 'INNER JOIN') {
        $alias = is_array($table) ? key($table) : $table;
        $table = is_array($table) ? current($table) : $table;

        $this->_joins[$alias] = array(
            'type'  => $joinType,
            'on'    => $on,
            'table' => $table
        );

        $this->_columns[$alias] = (array)$columns;

        return $this;
    }
}
