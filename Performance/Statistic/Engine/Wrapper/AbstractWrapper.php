<?php

namespace PF\Statistic\Engine\Wrapper;

use PF\Main\Database;
use PF\Statistic\Engine\Select;

/**
 * This script defines abstract class for wrapping statistic select with grouping select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
abstract class AbstractWrapper {

    /**
     * Alias for wrapped select.
     */
    const TABLE_NAME = 'data';

    /**
     * Wrapper select instance.
     *
     * @var Select
     */
    private $_wrapper;

    /**
     * It means whether wrapper select was compiled.
     *
     * @var boolean
     */
    private $_isCompiled = false;

    /**
     * Construct method.
     *
     * @param Database $database Database instance
     *
     * @return void
     */
    public function __construct(Database $database) {
        $this->_wrapper = $database->select();
    }

    /**
     * Abstract method for adding column which will be selected.
     *
     * @param string $column Column name of selected data
     *
     * @return AbstractWrapper
     */
    abstract public function addColumn($column);

    /**
     * Sets source select into wrapper select.
     *
     * @param Select $select Source select with data
     *
     * @return AbstractWrapper
     */
    public function setSourceSelect(Select $select) {
        $this->_wrapper->from(array(self::TABLE_NAME => $select), array());

        return $this;
    }

    /**
     * Returns compiled wrapper select.
     *
     * @return Database\Select
     */
    public function getSelect() {
        if ($this->_isCompiled === false) {
            $this->compile();
            $this->_isCompiled = true;
        }

        return $this->_wrapper;
    }

    /**
     * Function for complie wrapper select.
     *
     * @return AbstractWrapper
     */
    protected function compile() {
        return $this;
    }

    /**
     * Returns wrapper select.
     *
     * @return Select
     */
    protected function getWrapper() {
        return $this->_wrapper;
    }
}
