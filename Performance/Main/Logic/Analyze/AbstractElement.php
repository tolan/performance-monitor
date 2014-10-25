<?php

namespace PM\Main\Logic\Analyze;

/**
 * This script defines abstract class for logic element.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractElement {

    /**
     * Element which is catched in logic expression.
     *
     * @var string
     */
    private $_element;

    /**
     * Construct method.
     *
     * @param string $element Element which is catched in logic expression.
     *
     * @return void
     */
    public function __construct($element = null) {
        $this->_element = $element;
    }

    /**
     * Returns element which was catched in logic expression.
     *
     * @return string
     */
    public function getElement() {
        return $this->_element;
    }

    /**
     * Abstract method for set value of element.
     *
     * @param $value Value of element (operand, operator, bracket)
     */
    abstract function setValue($value);

    /**
     * Abstract method for get value of element.
     */
    abstract function getValue();

    /**
     * Abstarct method for convert element to string.
     */
    abstract function __toString();

    /**
     * Abstract method for helper method which return array with basic information about the element.
     */
    abstract function toArray();
}
