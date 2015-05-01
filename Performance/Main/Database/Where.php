<?php

namespace PM\Main\Database;

/**
 * This script defines class for WHERE part of MySQL statement.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Where extends Query {

    const PART_WHERE = 'where';

    /**
     * List of where conditions
     *
     * @var array
     */
    private $_where = array();

    /**
     * Get dynamic part of statement.
     *
     * @param string $part Name of part
     *
     * @return mixed
     */
    public function getPart($part = null) {
        $result = null;
        if ($part === self::PART_WHERE) {
            $result = $this->_where;
        } else {
            $result = parent::getPart($part);
        }

        return $result;
    }

    /**
     * Reset dynamic part to default value.
     *
     * @param string $part Name of part
     *
     * @return \PM\Main\Database\Where
     */
    public function resetPart($part = null) {
        if ($part === self::PART_WHERE) {
            $this->_where = array();
        } else {
            parent::resetPart($part);
        }

        return $this;
    }

    /**
     * It adds condition with AND operator.
     *
     * @param string $condition Codition with binding
     * @param mixed  $bind      Bind data
     *
     * @return \PM\Main\Database\Where
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
     * @return \PM\Main\Database\Where
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
                $bind          = is_object($condition['bind']) ? array($condition['bind']) : (array)$condition['bind'];
                $compiledBinds = array_merge($compiledBinds, $bind);
                $operator      = $condition['operator'];
                $statement     = (string)$condition['condition'];

                if ($result === '') {
                    $result = ' ('.$statement.')';
                } else {
                    $result .= ' '.$operator.' ('.$statement.')';
                }
            }
        }

        if (array_keys($compiledBinds) === range(0, count($compiledBinds) - 1) && count($compiledBinds) > 0 && substr_count($result, '?') === 1) {
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
     * @return \PM\Main\Database\Where
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
