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
    private $_statement = null;

    /**
     * Binding data.
     *
     * @var array
     */
    private $_bind = array();

    /**
     * Query resource response from MySQL
     *
     * @var mixed
     */
    private $_response = null;

    /**
     * Connection resource to database
     *
     * @var Performance_Main_Database_Connection
     */
    protected $_connection;

    /**
     * Construct method
     *
     * @param Performance_Main_Database_Connection $connection Connection to database
     */
    final public function __construct(Performance_Main_Database_Connection $connection) {
        $this->_connection = $connection;
    }

    /**
     * Returns all results from sql statement in array.
     *
     * @return array
     */
    public function fetchAll() {
        $this->preFetch();

        $this->_response = $this->execute($this->_statement, $this->_bind);

        return $this->_response->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Returns one result from sql statement as one row.
     *
     * @return array
     */
    public function fetchOne() {
        $this->preFetch();

        $this->_response = $this->execute($this->_statement, $this->_bind);

        return $this->_response->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * This method execute SQL statement with binding data. This method corresponds with PDO->prepare($statement)->execute($bind).
     *
     * @param string $statement SQL statement
     * @param array  $bind      Data to bind
     *
     * @return PDOStatement
     *
     * @throws Performance_Main_Database_Exception
     */
    public function execute($statement, $bind) {
        $answer = $this->_connection->prepare($statement);

        if ($answer->execute($bind) === false) {
            throw new Performance_Main_Database_Exception('Database error. Check sql statement: '.$this->_connection->errorInfo());
        }

        return $answer;
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

        $this->_statement = $sql;

        return $this;
    }

    /**
     * Convert instance to compiled SQL statement.
     *
     * @return string
     */
    public function __toString() {
        $this->preFetch();

        $statement = $this->_statement;
        foreach ($this->_bind as $key => $value) {
            if (is_numeric($key)) {
                $statement = preg_replace('/\?/', '\''.$value.'\'', $statement, 1);
            } else {
                $statement = str_replace($key, '\''.$value.'\'', $statement);
            }
        }

        return $statement;
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
     * Sets SQL statement for prepare method.
     *
     * @param string $statement SQL statement
     *
     * @return Performance_Main_Database_Query
     */
    protected function setStatement($statement) {
        $this->_statement = $statement;

        return $this;
    }

    /**
     * Sets bind data for execute method.
     *
     * @param array $bind Bind data
     *
     * @return Performance_Main_Database_Query
     */
    protected function setBind($bind) {
        $this->_bind = array_merge($this->_bind, $bind);

        return $this;
    }

    /**
     * Gets SQL statement.
     *
     * @return string
     */
    public function getStatement() {
        if ($this->_statement === null) {
            $this->compile();
        }

        return $this->_statement;
    }

    /**
     * Gets bind data.
     *
     * @return array
     */
    public function getBind() {
        if ($this->_statement === null) {
            $this->compile();
        }

        return $this->_bind;
    }

    /**
     * Gets connection to database.
     *
     * @return Performance_Main_Database_Connection
     */
    protected function getConnection() {
        return $this->_connection;
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
     * Method which could be called before fetch method and it call compile method for create SQL statement.
     *
     * @return Performance_Main_Database_Query
     *
     * @throws Performance_Main_Database_Exception Throws when SQL query is not created.
     */
    protected function preFetch() {
        $this->_statement = null;
        $this->_bind      = array();

        $this->compile();

        if ($this->_statement === null) {
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
            foreach ($data as $key => $item) {
                $items[$key] = $this->cleanData($item);
            }

            $data = $items;
        } elseif (is_string($data)) {
            $data = mysql_real_escape_string($data);
        } elseif (is_object($data)) {
            $data = (string)$data;
        } elseif (is_bool($data)) {
            $data = $data === true ? 1 : 0;
        }

        return $data;
    }
}
