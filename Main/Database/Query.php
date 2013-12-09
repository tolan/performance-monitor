<?php

/**
 * This script defines class for universal query statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Database_Query {

    /**
     * Created MySQL statement
     *
     * @var string
     */
    protected $sql = null;

    /**
     * Query resource from mysql_query
     *
     * @var mixed
     */
    protected $query = null;

    /**
     * Connection resource to database
     *
     * @var resource
     */
    protected $_connection;

    /**
     * Construct method
     *
     * @param resource $connection Connection resource to database
     */
    final public function __construct($connection) {
        $this->_connection = $connection;
    }

    /**
     * Returns all results from sql statement in array.
     *
     * @return array
     */
    public function fetchAll() {
        $this->preFetch();

        $query = $this->fetch($this->sql);

        $result = array();
        while($row = mysql_fetch_array($query, MYSQLI_ASSOC)) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Returns one result from sql statement as one row.
     *
     * @return array
     */
    public function fetchOne() {
        $this->preFetch();

        $query = $this->fetch($this->sql);

        return mysql_fetch_array($query, MYSQLI_ASSOC);
    }

    /**
     * This sets SQL statement.
     *
     * @param string $sql SQL statement
     *
     * @return Performance_Main_Database_Query
     *
     * @throws Performance_Main_Database_Exception Throws when input SQL is not statement.
     */
    public function setSQL($sql) {
        if (!is_string($sql)) {
            throw new Performance_Main_Database_Exception('Input is not SQL statement.');
        }

        $this->sql = $sql;

        return $this;
    }

    /**
     * Convert instance to compiled SQL statement.
     *
     * @return string
     */
    public function __toString() {
        $this->compile();

        return $this->sql;
    }

    /**
     * Convert instance to compiled SQL statement.
     *
     * @return string
     */
    public function assemble() {
        return (string)$this;
    }

    /**
     * Compile method of SQL statement. There is nothig because this query is compiled in input.
     *
     * @return Performance_Main_Database_Query
     */
    protected function compile() {
        return $this;
    }

    /**
     * This method sends mysql statement to database and returns resource.
     *
     * @param string $sql SQL statement
     *
     * @return mixed Resource to MySQL
     */
    protected function fetch($sql) {
        $this->setSQL($sql);
        $this->query = mysql_query($sql, $this->_connection);

        if ($this->query === false) {
            throw new Performance_Main_Database_Exception('Database error. Check sql statement: '.$sql);
        }

        return $this->query;
    }

    /**
     * Method which could be called before fetch method and it call compile method for create SQL statement.
     *
     * @return Performance_Main_Database_Query
     *
     * @throws Performance_Main_Database_Exception Throws when SQL query is not created.
     */
    protected function preFetch() {
        $this->compile();

        if ($this->sql === null) {
            throw new Performance_Main_Database_Exception('SQL query is not set!');
        }

        return $this;
    }

    /**
     * Helper method for cleaning data (it can prevent SQL injection).
     *
     * @param mixed $data Input data to clean
     *
     * @return mixed Cleaned data
     */
    protected function cleanData($data) {
        if (is_array($data)) {
            $items = array();
            foreach ($data as $item) {
                $items[] = $this->cleanData($item);
            }

            $data = $items;
        } elseif (is_string($data)) {
            $data = "'".mysql_real_escape_string($data)."'";
        } elseif (is_object($data)) {
            $data = (string)$data;
        } elseif (is_bool($data)) {
            $data = $data === true ? 1 : 0;
        } elseif (is_null($data)) {
            $data = 'NULL';
        }

        return $data;
    }
}
