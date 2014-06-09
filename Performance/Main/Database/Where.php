<?php

namespace PF\Main\Database;

/**
 * This script defines class for WHERE part of MySQL statement.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Where extends Query {

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
     * @return \PF\Main\Database\Where
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
     * @return \PF\Main\Database\Where
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
                $compiledBinds = array_merge($compiledBinds, (array)$condition['bind']);
                $operator      = $condition['operator'];
                $condition     = $condition['condition'];

                if ($result === '') {
                    $result = ' ('.$condition.')';
                } else {
                    $result .= ' '.$operator.' ('.$condition.')';
                }
            }
        }

        if (array_keys($compiledBinds) === range(0, count($compiledBinds) - 1) && count($compiledBinds) > 0) {
            $result = str_replace('?', join(', ', array_fill(0, count($compiledBinds), '?')), $result);
        }

        $this->setStatement($result);
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
     * @return \PF\Main\Database\Where
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
