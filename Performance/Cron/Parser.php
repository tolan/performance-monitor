<?php

namespace PM\Cron;

use PM\Main\Utils;

/**
 * This script defines class of the cron parser. It is for parsing cron expression.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 *
 * @method Parser\Date resolveNext()     Returns Date object for next run.
 * @method Parser\Date resolveBefore()   Returns Date object for before run.
 * @method Parser\Date resolveIsActual() Returns Date object with actual date or null.
 */
class Parser {

    /**
     * Expression instance.
     *
     * @var Parser\Expression
     */
    private $_expression;

    /**
     * Utils instance.
     *
     * @var Utils
     */
    private $_utils;

    /**
     * Construct method for set default expression.
     *
     * @param Utils             $utils      Utils instance
     * @param Parser\Expression $expression Parser expression instance
     *
     * @return void
     */
    public function __construct(Utils $utils, Parser\Expression $expression = null) {
        $this->_utils = $utils;

        if ($expression) {
            $this->setExpression($expression);
        }
    }

    /**
     * Setter for parser expression instance.
     *
     * @param Parser\Expression $expression Parser expression instance
     *
     * @return Parser
     */
    public function setExpression(Parser\Expression $expression) {
        $this->_expression = $expression;

        return $this;
    }

    /**
     * Setter for date instance.
     *
     * @param Parser\Date $datetime Cron data instance
     *
     * @return Parser
     */
    public function setDatetime(Parser\Date $datetime) {
        $this->_expression->setDatetime($datetime);

        return $this;
    }

    /**
     * Getter for parser expression instance.
     *
     * @return Parser\Expression
     */
    public function getExpression() {
        return $this->_expression;
    }

    /**
     * Magic method foc call resolve methods.
     *
     * @param string $name      Name of called method
     * @param array  $arguments Set of input arguments (it is not used)
     *
     * @return Parser\Date|null
     *
     * @throws Parser\Exception Throws when called method is not "resolve" method.
     */
    public function __call($name, $arguments) {
        $prefix = substr($name, 0, 7);

        if ($prefix !== 'resolve') {
            throw new Parser\Exception('Undefined method: '.$name);
        }

        $method = ucfirst($this->_utils->toCamelCase(substr($name, 7)));

        $class    = __NAMESPACE__.'\Parser\Resolver\\'.$method;
        $instance = new $class();
        /* @var $instance Parser\Resolver\AbstractResolver */
        $instance->setExpression($this->_expression);

        return $instance->getResult();
    }
}
