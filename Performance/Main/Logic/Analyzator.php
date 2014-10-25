<?php

namespace PM\Main\Logic;

/**
 * This script defines class for logic analyzator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Analyzator {

    /**
     * Logic expression.
     *
     * @var string
     */
    private $_expression = '';

    /**
     * Analyzed expression.
     *
     * @var \PM\Main\Logic\Analyze\AbstractElement|null
     */
    private $_logic = null;

    /**
     * Definition of expression algebra.
     *
     * @var array
     */
    private $_algebra = null;

    /**
     * Construct method.
     *
     * @return void
     */
    public function __construct() {
        $this->_algebra = array(
            'operators'     => Analyze\Enum\Operator::getConstants(),
            'operands'      => array('\d+'),
            'leftBrackets'  => array('[', '(', '{'),
            'rightBrackets' => array(']', ')', '}')
        );
    }

    /**
     * Sets logic expression for analyze.
     *
     * @param strin $expression Logic expression
     *
     * @return \PM\Main\Logic\Analyzator
     */
    public function setExpression($expression) {
        $this->_expression = $expression;
        $this->_logic      = null;

        return $this;
    }

    /**
     * Returns analyzed logic tree structure.
     *
     * @return \PM\Main\Logic\Analyze\AbstractElement|null
     */
    public function getLogic() {
        if ($this->_logic === null) {
            $this->_logic = $this->_analyze();
        }

        return $this->_logic;
    }

    /**
     * Returns definition of algebra.
     *
     * @return array
     */
    public function getAlgebra() {
        return $this->_algebra;
    }

    /**
     * Sets definition of expression algebra.
     *
     * @param array $algebra Definition of algebra.
     *
     * @return \PM\Main\Logic\Analyzator
     */
    public function setAlgebra($algebra) {
        // TODO validation
        $this->_algebra = $algebra;

        return $this;
    }

    /**
     * This analyze expression with algebra.
     *
     * @return \PM\Main\Logic\Analyze\AbstractElement|null
     */
    private function _analyze() {
        $expression = new Analyze\Parser($this->_expression, $this->_algebra);

        return $expression->analyze();
    }
}
