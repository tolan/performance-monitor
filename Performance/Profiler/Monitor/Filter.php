<?php

namespace PM\Profiler\Monitor;

use PM\Profiler\Entity;
use PM\Profiler\Monitor\Filter\Enum;

/**
 * This script defines class for monitor filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Filter implements Interfaces\Filter {

    /**
     * Characted type: positive | negative.
     *
     * @var enum One of PM\Profiler\Monitor\Filter\Enum\Type
     */
    private $_character;

    /**
     * Flag which allow check over whole call stack.
     *
     * @var boolean
     */
    private $_allowStack = true;

    /**
     * List of compiled filters.
     *
     * @var array
     */
    private $_compiled = array();

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Entity\Filter $filter Filter entity instance
     *
     * @return void
     */
    public function __construct(Entity\Filter $filter) {
        $this->_character = $filter->get('type', Enum\Type::POSITIVE);
        $this->_compile($filter->get('parameters', array()));
    }

    /**
     * Returns whether backtrace is allowed by filter rules.
     *
     * @param array $backtrace Backtrace
     *
     * @return boolean
     */
    public function isAllowedBacktrace($backtrace) {
        $isApplicable = $this->_isApplicable($backtrace);

        return $isApplicable === ($this->_character === Enum\Type::POSITIVE);
    }

    /**
     * Returns wheter filter is applicable to backtrace.
     *
     * @param array $backtrace Backtrace
     *
     * @return boolean
     */
    private function _isApplicable($backtrace) {
        $call['immersion'] = count($backtrace);
        $call['files']     = array();
        reset($backtrace);

        do {
            $levelCall = current($backtrace);

            if (isset($levelCall['line'])) {
                $call['files'][$levelCall['file']][] = $levelCall['line'];
            }
        } while ($this->_allowStack && next($backtrace));

        $isApplicable = $this->_evaluateImmersion($call);
        $isApplicable = $isApplicable && $this->_evaluateFile(array_keys($call['files']));
        $isApplicable = $isApplicable && $this->_evaluateLine($call['files']);

        return $isApplicable;
    }

    /**
     * Evaluate that backtrace has right immersion.
     *
     * @param array $call Call data
     *
     * @return boolean
     */
    private function _evaluateImmersion($call) {
        $isApplicable = true;

        if (isset($this->_compiled[Enum\Parameter::IMMERSION])) {
            $parameter = $this->_compiled[Enum\Parameter::IMMERSION];
            if ($parameter['operator'] === Enum\Operator::HIGHER_THAN && $call['immersion'] <= $parameter['value']) {
                $isApplicable = false;
            } elseif ($parameter['operator'] === Enum\Operator::LOWER_THAN && $call['immersion'] >= $parameter['value']) {
                $isApplicable = false;
            }
        }

        return $isApplicable;
    }

    /**
     * Evaluate that backtrace has right file.
     *
     * @param array $files List of files in backtrace
     *
     * @return boolean
     */
    private function _evaluateFile($files) {
        $isApplicable = true;

        if (isset($this->_compiled[Enum\Parameter::FILE])) {
            $parameter = $this->_compiled[Enum\Parameter::FILE];

            switch ($parameter['operator']) {
                case Enum\Operator::REG_EXP:
                    $isIn = false;
                    foreach ($files as $filename) {
                        $isIn = $isIn || (bool)preg_match('#'.$parameter['value'].'#', $filename);
                    }

                    $isApplicable = $isIn;
                    break;
            }
        }

        return $isApplicable;
    }

    /**
     * Evaluate that backtrace has right line.
     *
     * @param array $files List of files in backtrace
     *
     * @return boolean
     */
    private function _evaluateLine($files) {
        $isApplicable = true;

        if (isset($this->_compiled[Enum\Parameter::LINE])) {
            $filenames = array_keys($files);
            $parameter = $this->_compiled[Enum\Parameter::LINE];

            foreach ($filenames as $filename) {
                if ($this->_evaluateFile(array($filename)) === true) {
                    foreach ($files[$filename] as $line) {
                        if (($parameter['operator'] === Enum\Operator::LOWER_THAN && $line >= $parameter['value']) ||
                                ($parameter['operator'] === Enum\Operator::HIGHER_THAN && $line <= $parameter['value'])) {
                            $isApplicable = false;
                        }
                    }
                }
            }
        }

        return $isApplicable;
    }

    /**
     * Compile input parameters of filter.
     *
     * @param array $parameters Array with parameters of filter
     *
     * @return PM\Profiler\Monitor\Filter
     */
    private function _compile($parameters) {
        foreach ($parameters as $parameter) {
            $type = $parameter['parameter'];
            if ($type === Enum\Parameter::SUB_STACK) {
                $this->_allowStack = $parameter['value'];
            } else {
                $this->_compiled[$type] = $parameter;
            }
        }

        return $this;
    }
}
