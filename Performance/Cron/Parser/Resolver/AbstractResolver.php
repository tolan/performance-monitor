<?php

namespace PM\Cron\Parser\Resolver;

use PM\Cron\Parser;

/**
 * This script defines abstract class for parser date resolvers.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
abstract class AbstractResolver {

    /**
     * Parser expression instance.
     *
     * @var Parser\Expression
     */
    private $_expression = null;

    /**
     * Returns result of resolving process.
     *
     * @return Parser\Date|null
     */
    abstract public function getResult();

    /**
     * Setter for expression.
     *
     * @param Parser\Expression $expression Parser expression instance
     *
     * @return AbstractResolver
     */
    public function setExpression(Parser\Expression $expression) {
        $this->_expression = $expression;

        return $this;
    }

    /**
     * Returns instance of parser expression.
     *
     * @return Parser\Expression
     *
     * @throws Parser\Exception Throws when expression is not set.
     */
    protected function getExpression() {
        if ($this->_expression === null) {
            throw new Parser\Exception('Expression is not set.');
        }

        return $this->_expression;
    }
}
