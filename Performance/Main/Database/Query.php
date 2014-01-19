<?php

namespace PF\Main\Database;

/**
 * This script defines class for universal query statement of MySQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Query {

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
     * @var \PF\Main\Database\Connection
     */
    protected $_connection;

    /**
     * Construct method
     *
     * @param \PF\Main\Database\Connection $connection Connection to database
     */
    final public function __construct(Connection $connection) {
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

        return $this->_response->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Returns one result from sql statement as one row.
     *
     * @return array
     */
    public function fetchOne() {
        $this->preFetch();

        $this->_response = $this->execute($this->_statement, $this->_bind);

        return $this->_response->fetch(\PDO::FETCH_ASSOC);
    }

    /**
     * This method execute SQL statement with binding data. This method corresponds to PDO->prepare($statement)->execute($bind).
     *
     * @param string $statement SQL statement
     * @param array  $bind      Data to bind
     *
     * @return \PDOStatement
     *
     * @throws \PF\Main\Database\Exception
     */
    public function execute($statement, $bind = array()) {
        $answer = $this->_connection->prepare($statement);

        if ($answer->execute($bind) === false) {
            throw new Exception('Database error. Check sql statement: '.$this->_connection->errorInfo());
        }

        return $answer;
    }

    /**
     * This sets SQL statement.
     *
     * @param string $sql SQL statement
     *
     * @return \PF\Main\Database\Query
     *
     * @throws \PF\Main\Database\Exception Throws when input SQL is not statement.
     */
    public function setSQL($sql) {
        if (!is_string($sql)) {
            throw new Exception('Input is not SQL statement.');
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
        try {
            $this->preFetch();

            $statement = $this->_statement;

            if (substr_count($statement, '?') > 1 && substr_count($statement, '?') !== count(array_filter(array_keys($this->_bind), 'is_numeric'))) {
                throw new Exception('Statement can not be builded for wrong binding');
            } elseif(substr_count($statement, '?') === 1) {
                $numBinding = array_intersect_key($this->_bind, array_flip(array_filter(array_keys($this->_bind), 'is_numeric')));
                array_walk($numBinding, function(&$item) {
                    if (is_string($item)) {
                        $item = '\''.$item.'\'';
                    }
                });
                $statement  = str_replace('?', join(', ', $numBinding), $statement);
            }

            foreach ($this->_bind as $key => $value) {
                if (is_numeric($key)) {
                    $statement = preg_replace('/\?/', '\''.$value.'\'', $statement, 1);
                } else {
                    $value     = is_array($value) ? join(', ', $value) : $value;
                    $statement = str_replace($key, '\''.$value.'\'', $statement);
                }
            }
        } catch (Exception $exc) {
            trigger_error(get_class($exc).': '.$exc->getMessage(), E_USER_WARNING);
            $statement = '';
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
     * @return \PF\Main\Database\Query
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
     * @return \PF\Main\Database\Query
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
     * @return \PF\Main\Database\Connection
     */
    protected function getConnection() {
        return $this->_connection;
    }

    /**
     * Compile method of SQL statement. There is nothig because this query is compiled in input.
     *
     * @return \PF\Main\Database\Query
     */
    protected function compile() {
        return $this;
    }

    /**
     * Method which could be called before fetch method and it call compile method for create SQL statement.
     *
     * @return \PF\Main\Database\Query
     *
     * @throws \PF\Main\Database\Exception Throws when SQL query is not created.
     */
    protected function preFetch() {
        if (get_called_class() !== __CLASS__) {
            $this->_statement = null;
            $this->_bind      = array();
        }

        $this->compile();

        if ($this->_statement === null) {
            throw new Exception('SQL query is not set!');
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
