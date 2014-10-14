<?php

namespace PF\Main\Logic\Analyze\Element;

use PF\Main\Logic\Analyze\AbstractElement;
use PF\Main\Logic\Exception;

/**
 * This script defines class for logic operator element.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Operator extends AbstractElement {

    /**
     * Content of left element.
     *
     * @var AbstractElement|null
     */
    private $_left = null;

    /**
     * Content of right element.
     *
     * @var AbstractElement|null
     */
    private $_right = null;

    /**
     * Sets element as left or right element.
     *
     * @param AbstractElement|null $value Element of operator
     *
     * @return \PF\Main\Logic\Analyze\Element\Operator
     *
     * @throws \PF\Main\Logic\Exception Throws when operator is full.
     */
    public function setValue($value) {
        if ($this->_left === null) {
            $this->_left = $value;
        } elseif ($this->_right === null) {
            $this->_right = $value;
        } else {
            throw new Exception('Operator is full. Missing next operator or bracket.');
        }

        return $this;
    }

    /**
     * Returns value of left and right elements.
     *
     * @return array
     */
    public function getValue() {
        return array('left' => $this->_left, 'right' => $this->_right);
    }

    /**
     * Returns value in string format.
     *
     * @return string
     */
    public function __toString() {
        return $this->_left.' '.$this->getElement().' '.$this->_right;
    }

    /**
     * Helper method for better development and debugging.
     *
     * @return array
     */
    public function toArray() {
        return array('operator' => $this->getElement(), 'left' => $this->_left->toArray(), 'right' => $this->_right->toArray());
    }
}
