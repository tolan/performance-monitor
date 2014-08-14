<?php

namespace PF\Main\Logic\Evaluate;

use PF\Main\Logic\Evaluator;

/**
 * This script defines abstarct class for composing result of performer and extraction of extractor.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractComposer {

    /**
     * Evaluator instance
     *
     * @var \PF\Main\Logic\Evaluator
     */
    private $_evaluator;

    /**
     * Sets evaluator instance.
     *
     * @param \PF\Main\Logic\Evaluator $evaluator Evaluator instance
     *
     * @return \PF\Main\Logic\Evaluate\AbstractComposer
     */
    public function setEvaluator(Evaluator $evaluator) {
        $this->_evaluator = $evaluator;

        return $this;
    }

    /**
     * Returns evaluator instance.
     *
     * @return \PF\Main\Logic\Evaluator
     */
    final protected function getEvaluator() {
        return $this->_evaluator;
    }

    /**
     * Abstract method for composing result and original data.
     */
    abstract public function compose($performerResult);

    /**
     * Return result of composing.
     */
    abstract public function getResult();
}
