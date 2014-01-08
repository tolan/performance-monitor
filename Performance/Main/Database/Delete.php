<?php

namespace PF\Main\Database;

/**
 * This script defines class for delete statement of MySQL.
 * ATTENTION: This can delete whole table.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method \PF\Main\Database\Delete where(string $condition, array $bind=null)   It adds condition with AND operator.
 * @method \PF\Main\Database\Delete orWhere(string $condition, array $bind=null) It adds condition with OR operator.
 * @method \PF\Main\Database\Delete setSQL(string $sql)                          It sets SQL qeury.
 */
class Delete extends Where {

    /**
     * Name of table
     *
     * @var string
     */
    private $_table = null;

    /**
     * Method for set table.
     *
     * @param string $table Name of table
     *
     * @return \PF\Main\Database\Delete
     */
    public function setTable($table) {
        $this->_table = is_array($table) ? current($table) : $table;

        return $this;
    }

    /**
     * This runs SQL delete stament and returns count of affected rows.
     * ATTENTION: This can delete whole table.
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
     * @return \PF\Main\Database\Delete
     *
     * @throws \PF\Main\Database\Exception Throws when table is not set.
     */
    protected function compile() {
        if ($this->_table === null) {
            throw new Exception('Table is not set.');
        }

        $sql = 'DELETE FROM '.$this->_table;

        $where = parent::compile();
        $sql .= $where == '' ? '' : ' WHERE '.$where;

        $this->setStatement($sql);

        return $this;
    }
}
