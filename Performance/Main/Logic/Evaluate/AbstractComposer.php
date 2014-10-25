<?php

namespace PM\Main\Logic\Evaluate;

use PM\Main\Logic\Evaluator;

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
     * @var \PM\Main\Logic\Evaluator
     */
    private $_evaluator;

    /**
     * Sets evaluator instance.
     *
     * @param \PM\Main\Logic\Evaluator $evaluator Evaluator instance
     *
     * @return \PM\Main\Logic\Evaluate\AbstractComposer
     */
    public function setEvaluator(Evaluator $evaluator) {
        $this->_evaluator = $evaluator;

        return $this;
    }

    /**
     * Returns evaluator instance.
     *
     * @return \PM\Main\Logic\Evaluator
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
