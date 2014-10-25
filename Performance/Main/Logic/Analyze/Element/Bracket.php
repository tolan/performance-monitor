<?php

namespace PM\Main\Logic\Analyze\Element;

use PM\Main\Logic\Analyze\AbstractElement;
use PM\Main\Logic\Exception;

/**
 * This script defines class for logic bracket element.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Bracket extends AbstractElement {

    /**
     * This value represents analyzed tree
     *
     * @var AbstractElement|null
     */
    private $_value = null;

    /**
     * Default value for default representations of left bracket.
     *
     * @var string
     */
    private $_leftBracket = '(';

    /**
     * Default value for default representations of right bracket.
     *
     * @var string
     */
    private $_rightBracket = ')';

    /**
     * Setter for content of brackets.
     *
     * @param AbstractElement|null $value
     *
     * @return \PM\Main\Logic\Analyze\Element\Bracket
     *
     * @throws \PM\Main\Logic\Exception Throws when input value has bad format or bracket is full.
     */
    public function setValue($value) {
        if ($this->_value === null) {
            $this->_value = $value;
        } elseif ($this->_value instanceof Operand) {
            if ($value instanceof Operator || $value instanceof Bracket) {
                $value->setValue($this->_value);
                $this->_value = $value;
            } else {
                throw new Exception('Bracket already has operand. Missing operator.');
            }
        } elseif($this->_value instanceof Operator || $this->_value instanceof Bracket) {
            $this->_value->setValue($value);
        } else {
            throw new Exception('Bracket is full. Invalid input.');
        }

        return $this;
    }

    /**
     * Returns value (analyzed tree).
     *
     * @return AbstractElement|null
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Formats content back into brackets.
     *
     * @return string
     */
    public function __toString() {
        return $this->_leftBracket.$this->_value.$this->_rightBracket;
    }

    /**
     * Helper method for better development and debugging.
     *
     * @return array
     */
    public function toArray() {
        return array('bracket' => $this->_value->toArray());
    }

    /**
     * Sets character of left bracket (default: '(').
     *
     * @param string $leftBracket Character of left bracket
     *
     * @return \PM\Main\Logic\Analyze\Element\Bracket
     */
    public function setLeftBracket($leftBracket) {
        $this->_leftBracket = $leftBracket;

        return $this;
    }

    /**
     * Sets character of right bracket (default: '(').
     *
     * @param string $rightBracket Character of right bracket
     *
     * @return \PM\Main\Logic\Analyze\Element\Bracket
     */
    public function setRightBracket($rightBracket) {
        $this->_rightBracket = $rightBracket;

        return $this;
    }
}
