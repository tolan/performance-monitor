<?php

/**
 * This script defines class for WHERE part of MySQL statement.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Database_Where extends Performance_Main_Database_Query {

    /**
     * List of where conditions
     *
     * @var array
     */
    private $_where = array();

    /**
     * It adds condition with AND operator.
     *
     * @param string $condition Codition with binding
     * @param mixed  $bind      Bind data
     *
     * @return Performance_Main_Database_Where
     */
    public function where($condition, $bind=null) {
        return $this->_whereCondition($condition, $bind, 'AND');
    }

    /**
     * It adds condition with OR operator.
     *
     * @param string $condition Codition with binding
     * @param mixed  $bind      Bind data
     *
     * @return Performance_Main_Database_Where
     */
    public function orWhere($condition, $bind=null) {
        return $this->_whereCondition($condition, $bind, 'OR');
    }

    /**
     * It creates condition string from WHERE conditions.
     *
     * @return string
     */
    protected function compile() {
        $result = '';
        $compiledBinds = array();

        if (count($this->_where) > 0) {
            foreach ($this->_where as $condition) {
                $bind          = $this->cleanData($condition['bind']);
                $compiledBinds = array_merge($compiledBinds, (array)$bind);
                $operator      = $condition['operator'];
                $condition     = $condition['condition'];

                if ($result === '') {
                    $result = ' ('.$condition.')';
                } else {
                    $result .= ' '.$operator.' ('.$condition.')';
                }
            }
        }

        $this->setBind($compiledBinds);

        return $result;
    }

    /**
     * This method adds condition to list of conditions.
     *
     * @param string $condition Codition with binding
     * @param mixed  $bind      Bind data
     * @param string $type      Type of operator (AND or OR)
     *
     * @return Performance_Main_Database_Where
     */
    private function _whereCondition($condition, $bind=null, $type = 'AND') {
        $this->_where[] = array(
            'operator'  => $type,
            'condition' => $condition,
            'bind'      => $bind
        );

        return $this;
    }
}
