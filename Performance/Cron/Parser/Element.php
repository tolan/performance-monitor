<?php

namespace PM\Cron\Parser;

/**
 * This script defines class for parser element.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Element {

    /**
     * Expression element (part of datetime)
     *
     * @var string
     */
    private $_expression;

    /**
     * Minimal value.
     *
     * @var int
     */
    private $_min = 0;

    /**
     * Maximal value.
     *
     * @var int
     */
    private $_max = 0;

    /**
     * Parsed expression element (parsed all posible values).
     *
     * @var array
     */
    private $_parsed = null;

    /**
     * Actual value for compare
     *
     * @var int
     */
    private $_actual;

    /**
     * Setter for expression element.
     *
     * @param string $expression Expression element (part of date)
     *
     * @return Element
     */
    public function setExpression($expression = '*') {
        $this->_parsed     = null;
        $this->_expression = $expression;

        return $this;
    }

    /**
     * Setter for actual value for compare.
     *
     * @param int $actual Actual value
     *
     * @return Element
     */
    public function setActual($actual) {
        $this->_actual = $actual;

        return $this;
    }

    /**
     * Returns actual value.
     *
     * @return int
     */
    public function getActual() {
        return $this->_actual;
    }

    /**
     * Setter for minimal value.
     *
     * @param int $min Minimal value
     *
     * @return Element
     */
    public function setMin($min = 0) {
        $this->_parsed = null;
        $this->_min    = $min;

        return $this;
    }

    /**
     * Setter for maximal value.
     *
     * @param int $max Maximal value
     *
     * @return Element
     */
    public function setMax($max = 0) {
        $this->_parsed = null;
        $this->_max    = $max;

        return $this;
    }

    /**
     * Returns all posible values for element with leading zeros.
     *
     * @return array
     */
    public function parse() {
        if ($this->_parsed === null) {
            $result = array();
            $parts  = explode(',', $this->_expression);

            foreach ($parts as $part) {
                $result = array_merge($result, $this->_resolvePart($part));
            }

            sort($result);

            $this->_parsed = array_unique($result);
        }

        return $this->_parsed;
    }

    /**
     * Return value after actual with leading zeros.
     *
     * @param int $add Addition for actual value
     *
     * @return string
     */
    public function getNext($add = 0) {
        $parsed = $this->parse();
        $result = $this->getFirst();
        $actual = $this->_actual + $add;

        if ($actual > $this->_max) {
            $actual -= $this->_max;
        }

        foreach ($parsed as $item) {
            if ($actual <= $item) {
                $result = $item;
                break;
            }
        }

        return $result;
    }

    /**
     * Returns first value for expression with leading zeros.
     *
     * @return string
     */
    public function getFirst() {
        $parsed = $this->parse();

        return $parsed[0];
    }

    /**
     * Returns last value for expression with leading zeros.
     *
     * @return string
     */
    public function getLast() {
        $parsed = $this->parse();

        return $parsed[count($parsed) - 1];
    }

    /**
     * Return value before actual with leading zeros.
     *
     * @param int $sub Subtract for actual value
     *
     * @return string
     */
    public function getBefore($sub = 0) {
        $parsed = $this->parse();
        $result = $this->getLast();
        $actual = $this->_actual - $sub;

        if ($actual < $this->_min) {
            $actual += $this->_max;
        }

        foreach (array_reverse($parsed) as $item) {
            if ($actual >= $item) {
                $result = $item;
                break;
            }

        }

        return $result;
    }

    /**
     * Returns whether the actual value is in expression.
     *
     * @return boolean
     */
    public function isActual() {
        $parsed = $this->parse();

        return in_array($this->getActual(), $parsed);
    }

    /**
     * Returns whether the expression contains all posible value between min and max.
     *
     * @return boolean
     */
    public function isFullRange() {
        return count($this->parse()) === ($this->_max - $this->_min + 1);
    }

    /**
     * It resolves part of expression.
     *
     * @param string $part Part of expression
     *
     * @return string
     */
    private function _resolvePart($part) {
        $result = array();
        if (strstr($part, '/')) {
            $result = $this->_resolvePartWithDivision($part);
        } else {
            $result = $this->_resolvePartDefault($part);
        }

        return $result;
    }

    /**
     * It resolves part of expression which has '/'.
     *
     * @param string $part Part of expression
     *
     * @return string
     */
    private function _resolvePartWithDivision($part) {
        list($numerator, $denominator) = explode('/', $part);
        $numerators = $this->_resolvePartDefault($numerator);

        $result = array();
        foreach ($numerators as $numerator) {
            if (($numerator % $denominator) === 0) {
                $result[] = $numerator;
            }
        }

        return $result;
    }

    /**
     * It resolves part of expression between max and min borders.
     *
     * @param string $part Part of expression
     *
     * @return string
     */
    private function _resolvePartDefault($part) {
        $result = array();
        $len    = max(strlen($this->_max), strlen($this->_min));

        if (strstr($part, '-')) {
            list ($min, $max) = explode('-', $part);
        } elseif ($part === '*') {
            $min = $this->_min;
            $max = $this->_max;
        } else  {
            $min = $part;
            $max = $part;
        }

        for($i = $min; $i <= $max; $i++) {
            $result[] = str_pad($i, $len, '0', STR_PAD_LEFT);
        }

        return $result;
    }
}
