<?php

namespace PF\Search\Filter\Condition;

use PF\Search\Filter\Select;

/**
 * This script defines class for filter condition with query type. It is very special condition because override method addFilter where is special function.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Query extends AbstractCondition {

    private $_where = null;

    /**
     * It adds filter to select with table name and column. It take input query string and find each words.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     * @param string                   $table  Table name where is column
     * @param string                   $column Column name where will be value
     *
     * @return \PF\Search\Filter\Condition\AbstractCondition
     */
    public function addFilter(Select $select, $table, $column) {
        $values = $this->_extractStrings($this->getValue());

        if ($this->_where === null && !empty($values)) {
            $this->_where = $select->createWhere();
        }

        foreach ($values as $value) {
            $this->_where->orWhere($table.'.'.$column.' LIKE ?', '%'.$value.'%');
        }

        return $this;
    }

    /**
     * This method extracts words and string in ".
     *
     * @param string $string String to extracting
     *
     * @return string
     */
    private function _extractStrings($string) {
        $result   = array();
        $inString = false;

        $tmp   = explode('"', $string);
        $count = count($tmp);

        for($i = 0; $i < $count; $i++) {
            if ($inString) {
                $result[] = $tmp[$i];
            } else {
                $result = array_merge($result, explode(' ', $tmp[$i]));
            }

            $inString = !$inString;
        }

        return array_filter($result);
    }

    /**
     * Special method for fulltext. It takes all stored where conditions and inject it to original select.
     *
     * @param \PF\Search\Filter\Select $select Select instance
     *
     * @return \PF\Search\Filter\Condition\Query
     */
    public function fulltext(Select $select) {
        if ($this->_where !== null) {
            $select->where($this->_where->getStatement(), $this->_where->getBind());
        }

        return $this;
    }
}
