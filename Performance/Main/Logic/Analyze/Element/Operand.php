<?php

namespace PF\Main\Logic\Analyze\Element;

use PF\Main\Logic\Analyze\AbstractElement;
use PF\Main\Logic\Exception;

/**
 * This script defines class for logic operand element.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Operand extends AbstractElement {

    /**
     * Value of operand.
     *
     * @var mixed
     */
    private $_value = null;

    /**
     * Sets value of operand.
     *
     * @param mixed $value Value of operand
     *
     * @return \PF\Main\Logic\Analyze\Element\Operand
     *
     * @throws \PF\Main\Logic\Exception Throws when operand is full.
     */
    public function setValue($value) {
        if ($this->_value === null) {
            $this->_value = $value;
        } else {
            throw new Exception('Operand is full.');
        }

        return $this;
    }

    /**
     * Returns value of operand.
     *
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }

    /**
     * Returns value in string format.
     *
     * @return string
     */
    public function __toString() {
        return (string)$this->_value;
    }

    /**
     * Helper method for better development and debugging.
     *
     * @return array
     */
    public function toArray() {
        return array('operand' => $this->_value);
    }
}
