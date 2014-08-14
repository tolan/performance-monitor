<?php

namespace PF\Main\Logic\Evaluate;

use PF\Main\Logic\Evaluator;

/**
 * This script defines class for extractor for extracting input data for performer.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class AbstractExtractor {

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
     * @return \PF\Main\Logic\Evaluate\AbstractExtractor
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
     * Returns extracted scope range.
     */
    abstract public function getScope();

    /**
     * Returns flag that scope is set into extractor.
     */
    abstract public function isSetScope();

    /**
     * Returns extrated data range.
     */
    abstract public function getData($name);
}
