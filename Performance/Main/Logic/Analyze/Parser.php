<?php

namespace PM\Main\Logic\Analyze;

use PM\Main\Logic\Exception;

/**
 * This script defines class for logic expression parser.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Parser {

    /**
     * Logic expression.
     *
     * @var string
     */
    private $_expression;

    /**
     * Expression algebra which defines operators, operands and brackets characters.
     *
     * @var array
     */
    private $_algebra;

    /**
     * Analyzed tree structure.
     *
     * @var AbstractElement|null
     */
    private $_tree = null;

    /**
     * Construct method for set expression and algebra.
     *
     * @param string $expression Logic expression
     * @param array  $algebra    Expression algebra
     *
     * @return void
     */
    public function __construct($expression, $algebra) {
        $this->_expression = $expression;
        $this->_algebra    = $algebra;

        $this->_checkBracketsPairs($expression);
    }

    /**
     * This method analyze logic expression and returns analyzed tree structure of this.
     *
     * @return AbstractElement|null
     */
    public function analyze() {
        if ($this->_tree === null) {
            $this->_tree = $this->_getTree();
        }

        return $this->_tree;
    }

    /**
     * Create analyzed tree structure.
     *
     * @return AbstractElement|null
     *
     * @throws \PM\Main\Logic\Exception Throws when logic has unexpected character.
     */
    private function _getTree() {
        $expression = ltrim($this->_expression);
        $tree       = null;
        $length     = strlen($expression);

        while(strlen($expression) > 0 && $length > 0) {
            $match = array();
            foreach ($this->_algebra['operators'] as $operator) {
                if (preg_match('/^'.$operator.'/', $expression, $match)) {
                    $tree = $this->_detectOperator($match[0], $tree);
                    break;
                }
            }

            if (empty($match)) {
                foreach ($this->_algebra['leftBrackets'] as $brackets) {
                    if (preg_match('/^\\'.$brackets.'/', $expression, $match)) {
                        $tree = $this->_detectLeftBracket($match[0], $expression, $tree);
                        break;
                    }
                }
            }

            if (empty($match)) {
                foreach ($this->_algebra['rightBrackets'] as $brackets) {
                    if (preg_match('/^\\'.$brackets.'/', $expression, $match)) {
                        break;
                    }
                }
            }

            if (empty($match)) {
                foreach ($this->_algebra['operands'] as $operand) {
                    if (preg_match('/^'.$operand.'/', $expression, $match)) {
                        $tree = $this->_detectOperand($match[0], $tree);
                        break;
                    }
                }
            }

            if (empty($match)) {
                throw new Exception('Unexpected character in logic: '.$this->_expression);
            }

            $expression = ltrim(substr($expression, strlen($match[0])));

            $length -= empty($match[0]) ? 1 : strlen($match[0]); // prevent for neverending loop
        }

        return $tree;
    }

    /**
     * Method is called when parser detect operator.
     *
     * @param string                                 $operator Operator character set
     * @param \PM\Main\Logic\Analyze\AbstractElement $tree     Analyzed tree structure
     *
     * @return \PM\Main\Logic\Analyze\Element\Operator
     */
    private function _detectOperator($operator, AbstractElement $tree=null) {
        $operator = new Element\Operator($operator);

        if ($tree instanceof Element\Bracket) {
            $tree->setValue($operator);
        } elseif ($tree instanceof AbstractElement) {
            $operator->setValue($tree);
            $tree = $operator;
        } else {
        }

        return $tree;
    }

    /**
     * Method is called when parser detect left bracket.
     *
     * @param string                                 $bracket    Left bracket character
     * @param string                                 $expression Actual remaining logic expression
     * @param \PM\Main\Logic\Analyze\AbstractElement $tree       Analyzed tree structure
     *
     * @return \PM\Main\Logic\Analyze\Element\Bracket
     */
    private function _detectLeftBracket($bracket, &$expression, AbstractElement $tree=null) {
        $content = $this->_findBracketContent($expression, $bracket);
        $parser  = new self($content, $this->_algebra);

        $expression   = substr_replace($expression, '', strpos($expression, $bracket) + 1, strlen($content));
        $leftBracket  = $bracket;
        $rigthBracket = $this->_algebra['rightBrackets'][array_search($bracket, $this->_algebra['leftBrackets'])];

        $bracket = new Element\Bracket();
        $bracket->setLeftBracket($leftBracket)
                ->setRightBracket($rigthBracket)
                ->setValue($parser->analyze());

        if ($tree === null) {
            $tree = $bracket;
        } else {
            $tree->setValue($bracket);
        }

        return $tree;
    }

    /**
     * Method is called when parser detect operand.
     *
     * @param string                                 $operand Operand character set
     * @param \PM\Main\Logic\Analyze\AbstractElement $tree    Analyzed tree structure
     *
     * @return \PM\Main\Logic\Analyze\Element\Bracket
     *
     * @throws \PM\Main\Logic\Exception Throws when in logic expression missing operator between two operands
     */
    private function _detectOperand($operand, AbstractElement $tree=null) {
        $operand = (new Element\Operand($operand))->setValue($operand);

        if ($tree === null) {
            $tree = $operand;
        } elseif ($tree instanceof Element\Operator || $tree instanceof Element\Bracket) {
            $tree->setValue($operand);
        } else {
            throw new Exception ('Missing operator.');
        }

        return $tree;
    }

    /**
     * This method finds content in bracket in expression by definition of left bracket.
     *
     * @param string $expression  Logic expression
     * @param string $leftbracket Character of left bracket
     *
     * @return string
     *
     * @throws \PM\Main\Logic\Exception Throws when missing left or right bracket in expression
     */
    private function _findBracketContent($expression, $leftbracket) {
        $counter      = 0;
        $content      = '';
        $rigthBracket = $this->_algebra['rightBrackets'][array_search($leftbracket, $this->_algebra['leftBrackets'])];

        if (strpos($expression, $leftbracket) !== false) {
            $expression = substr($expression, strpos($expression, $leftbracket));

            while(strlen($expression) > 0) {
                $leftPos  = strpos($expression, $leftbracket);
                $rightPos = strpos($expression, $rigthBracket);

                if ($leftPos !== false && $rightPos !== false) {
                    if ($leftPos < $rightPos) {
                        $pos = $leftPos;
                        $counter++;
                    } else {
                        $pos = $rightPos;
                        $counter--;
                    }
                } elseif ($leftPos === false && $rightPos !== false) {
                    $pos = $rightPos;
                    $counter--;
                } elseif ($leftPos !== false && $rightPos == false) {
                    $pos = $leftPos;
                    $counter++;
                } else {
                    $pos = 0;
                    $counter = -1;
                }

                $content    .= substr($expression, 0, $pos + 1);
                $expression  = substr($expression, $pos + 1);

                if ($counter < 0) {
                    throw  new Exception('Missing left bracket.');
                }

                if ($counter === 0) {
                    break;
                }
            }
        }

        if ($counter > 0) {
            throw new Exception('Missing right bracket.');
        }

        return substr($content, 1, -1);
    }

    /**
     * It checks that the epxression has right count of left and right brackets.
     *
     * @param string $expression Logic expression
     *
     * @return \PM\Main\Logic\Analyze\Parser
     *
     * @throws \PM\Main\Logic\Exception Throws when count of left and right brackets is not same.
     */
    private function _checkBracketsPairs($expression) {
        $leftCount  = 0;
        $rightCount = 0;

        foreach ($this->_algebra['leftBrackets'] as $left) {
            $leftCount += substr_count($expression, $left);
        }

        foreach ($this->_algebra['rightBrackets'] as $right) {
            $rightCount += substr_count($expression, $right);
        }

        if ($rightCount > $leftCount) {
            throw new Exception('Missing left bracket.');
        }

        if ($rightCount < $leftCount) {
            throw new Exception('Missing right bracket.');
        }

        return $this;
    }
}
