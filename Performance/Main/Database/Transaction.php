<?php

namespace PM\Main\Database;

/**
 * This script defines class for managing transations with MySQL database.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Transaction {

    /**
     * Connection instance.
     *
     * @var \PM\Main\Database\Connection
     */
    private $_connection = null;

    /**
     * List of uncommited transactions (LIFO principle)
     *
     * @var array
     */
    private $_transactions = array();

    /**
     * Construct method.
     *
     * @param \PM\Main\Database\Connection $connection Connection instance to MySQL
     *
     * @return void
     */
    public function __construct(Connection $connection) {
        $this->_connection = $connection;
    }

    /**
     * Begins a transaction if no transaction has not begun.
     *
     * @param string $name Identification of transaction [optional]
     *
     * @return \PM\Main\Database\Transaction
     */
    public function begin($name = null) {
        if ($name === null) {
            $name = $this->_generateName();
        }

        if ($this->_isInTransaction() === false) {
            $this->_connection->prepare('')->closeCursor();
            $this->_connection->beginTransaction();
        }

        $this->_transactions[] = $name;

        return $this;
    }

    /**
     * Commits transaction by given name or last started.
     *
     * @param string $name Identification of transaction [optional]
     *
     * @return \PM\Main\Database\Transaction
     *
     * @throws \PM\Main\Database\Exception Throws when you commit without started transaction.
     */
    public function commit($name=null) {
        if ($this->_isInTransaction()) {
            $this->clear($name);

            if (empty($this->_transactions)) {
                $this->_connection->prepare('')->closeCursor();
                $this->_connection->commit();
            }
        } else {
            throw new Exception('You cannot commit database without transaction.');
        }

        return $this;
    }

    /**
     * Commits all transactions.
     *
     * @return \PM\Main\Database\Transaction
     */
    public function commitAll() {
        if ($this->_isInTransaction()) {
            $this->clearAll();

            $this->_connection->prepare('')->closeCursor();
            $this->_connection->commit();
        }

        return $this;
    }

    /**
     * Clear all stored transactions from storage.
     *
     * @return \PM\Main\Database\Transaction
     */
    public function clearAll() {
        $this->_transactions = array();

        return $this;
    }

    /**
     * Clear stored transaction by given name or last transaction.
     *
     * @param string $name Identification of transaction [optional]
     *
     * @return \PM\Main\Database\Transaction
     */
    public function clear($name=null) {
        if ($name === null) {
            array_pop($this->_transactions);
        } else {
            $key = array_search($name, $this->_transactions);

            if ($key >= 0) {
                array_splice($this->_transactions, $key, count($this->_transactions));
            }
        }

        return $this;
    }

    /**
     * Roll back transactions.
     *
     * @return \PM\Main\Database\Transaction
     */
    public function rollBack() {
        $this->_connection->prepare('')->closeCursor();
        $this->_connection->rollBack();

        return $this;
    }

    /**
     * Returns flag that database is in transaction.
     *
     * @return boolean
     */
    public function inTransaction() {
        return $this->_isInTransaction();
    }

    /**
     * Returns flag that database is in transaction.
     *
     * @return boolean
     *
     * @throws \PM\Main\Database\Exception Throws when database is in transaction but here is no stored transactions.
     */
    private function _isInTransaction() {
        $this->_connection->prepare('')->closeCursor();
        $inTransaction = $this->_connection->inTransaction();

        if ($inTransaction === false && !empty($this->_transactions)) {
            throw new Exception('Database is not in trasaction but storage is not cleared.');
        }

        return $inTransaction;
    }

    /**
     * Returns unique transaction name.
     *
     * @return string
     */
    private function _generateName() {
        return uniqid('transaction_');
    }
}
