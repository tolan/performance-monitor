<?php

namespace PF\Main\Logic\Evaluate;

use PF\Main\Logic\Evaluator;
use PF\Main\Logic\Analyze\AbstractElement;
use PF\Main\Logic\Analyze\Element;
use PF\Main\Logic\Exception;
use PF\Main\Logic\Analyze\Enum\Operator;

/**
 * This script defines class for performer for evaluate expression and data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractPerformer {

    /**
     * Extractor instance.
     *
     * @var \PF\Main\Logic\Evaluate\AbstractExtractor
     */
    private $_extractor = null;

    /**
     * Composer instance.
     *
     * @var \PF\Main\Logic\Evaluate\AbstractComposer
     */
    private $_composer = null;

    /**
     * Returns extractor for performer.
     *
     * @return \PF\Main\Logic\Evaluate\AbstractExtractor
     */
    final public function getExtractor() {
        if ($this->_extractor === null) {
            $this->_extractor = $this->createExtractor();
        }

        return $this->_extractor;
    }

    /**
     * Sets extractor for performer.
     *
     * @param \PF\Main\Logic\Evaluate\AbstractExtractor $extractor Extractor instance
     *
     * @return \PF\Main\Logic\Evaluate\AbstractPerformer
     */
    final public function setExtractor(AbstractExtractor $extractor) {
        $this->_extractor = $extractor;

        return $this;
    }

    /**
     * Returns composer for performer.
     *
     * @return \PF\Main\Logic\Evaluate\AbstractComposer
     */
    final public function getComposer() {
        if ($this->_composer === null) {
            $this->_composer = $this->createComposer();
        }

        return $this->_composer;
    }

    /**
     * Sets composer for performer.
     *
     * @param \PF\Main\Logic\Evaluate\AbstractComposer $composer Composer instance
     *
     * @return \PF\Main\Logic\Evaluate\AbstractPerformer
     */
    final public function setComposer(AbstractComposer $composer) {
        $this->_composer = $composer;

        return $this;
    }

    /**
     * This method perform evaluate of expression and data. Returns evaluated data with information about aplied operands.
     *
     * @param \PF\Main\Logic\Evaluator $evaluator Evaluator instance
     *
     * @return mixed
     */
    final public function perform(Evaluator $evaluator) {
        $this->getExtractor()->setEvaluator($evaluator);

        $result = $this->_evaluate($evaluator->getLogic());

        if ($this->getExtractor()->isSetScope()) {
            $result = $this->perform_and($result, $this->getExtractor()->getScope());
        }

        $this->getComposer()
            ->setEvaluator($evaluator)
            ->compose($result);

        return $this->getComposer()->getResult();
    }

    /**
     * This method provide evaluate of logic and data.
     *
     * @param \PF\Main\Logic\Analyze\AbstractElement $logic Analyzed logic tree
     *
     * @return mixed
     *
     * @throws \PF\Main\Logic\Exception Throws when operator is not supported
     */
    final private function _evaluate(AbstractElement $logic) {
        $result = null;

        if ($logic instanceof Element\Operand) {
            $result = $this->getExtractor()->getData($logic->getValue());
        } elseif ($logic instanceof Element\Bracket) {
            $result = $this->_evaluate($logic->getValue());
        } elseif ($logic instanceof Element\Operator) {
            $value = $logic->getValue();

            $leftOperand  = $this->_evaluate($value['left']);
            $rightOperand = $this->_evaluate($value['right']);

            switch ($logic->getElement()) {
                case Operator::OP_AND:
                    $result = $this->perform_and($leftOperand, $rightOperand);
                    break;
                case Operator::OP_OR:
                    $result = $this->perform_or($leftOperand, $rightOperand);
                    break;
                case Operator::OP_NAND:
                    $result = $this->perform_nand($leftOperand, $rightOperand);
                    break;
                case Operator::OP_NOR:
                    $result = $this->perform_nor($leftOperand, $rightOperand);
                    break;
                case Operator::OP_XOR:
                    $result = $this->perform_xor($leftOperand, $rightOperand);
                    break;
                case Operator::OP_XNOR:
                    $result = $this->perform_xnor($leftOperand, $rightOperand);
                    break;
            }
        } else {
            throw new Exception('Unsupported logic element.');
        }

        return $result;
    }

    /**
     * Abstract perform method for operator AND.
     */
    abstract protected function perform_and($first, $second);

    /**
     * Abstract perform method for operator OR.
     */
    abstract protected function perform_or($first, $second);

    /**
     * Abstract perform method for operator NAND.
     */
    abstract protected function perform_nand($first, $second);

    /**
     * Abstract perform method for operator NOR.
     */
    abstract protected function perform_nor($first, $second);

    /**
     * Abstract perform method for operator XOR.
     */
    abstract protected function perform_xor($first, $second);

    /**
     * Abstract perform method for operator XNOR.
     */
    abstract protected function perform_xnor($first, $second);

    /**
     * Abstract method for create default extractor.
     */
    abstract protected function createExtractor();

    /**
     * Abstract method for create default composer.
     */
    abstract protected function createComposer();
}
