<?php

namespace PM\Cron\Parser;

/**
 * This script defines class for cron expression.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Expression {

    /**
     * Minute part.
     *
     * @var Element
     */
    private $_minute;

    /**
     * Hour part.
     *
     * @var Element
     */
    private $_hour;

    /**
     * Day part.
     *
     * @var Element
     */
    private $_day;

    /**
     * Month part.
     *
     * @var Element
     */
    private $_month;

    /**
     * Day of week part.
     *
     * @var Element
     */
    private $_dayOfWeek;

    /**
     * Element factory instance.
     *
     * @var ElementFactory
     */
    private $_factory;

    /**
     * Cron date time instance.
     *
     * @var Date
     */
    private $_datetime;

    /**
     * Construct method for set dependencies.
     *
     * @param ElementFactory $factory Element factory instance
     * @param Date           $date    Cron date instance
     *
     * @return void
     */
    public function __construct(ElementFactory $factory, Date $date) {
        $this->_factory  = $factory;
        $this->_datetime = $date;
        $this->setExpression('* * * * *');
    }

    /**
     * Setter for acutal datetime.
     *
     * @param Date $date Cron date instance
     *
     * @return Expression
     */
    public function setDatetime(Date $date) {
        $this->_datetime = $date;

        $this->_minute->setActual($date->format('i'));
        $this->_hour->setActual($date->format('H'));
        $this->_day->setActual($date->format('d'));
        $this->_dayOfWeek->setActual($date->format('w'));
        $this->_month->setActual($date->format('m'));

        return $this;
    }

    /**
     * Returns instance of actual cron datetime.
     *
     * @return Date
     */
    public function getDatetime() {
        return $this->_datetime;
    }

    /**
     * Setter for cron expression string.
     *
     * @param string $expression Cron expression string
     *
     * @return Expression
     */
    public function setExpression($expression) {
        $parsed = $this->_parseExpression($expression);

        $this->setMinute($parsed['minute']);
        $this->setHour($parsed['hour']);
        $this->setDay($parsed['day']);
        $this->setMonth($parsed['month']);
        $this->setDayOfWeek($parsed['dayOfWeek']);

        $this->setDatetime($this->_datetime);

        return $this;
    }

    /**
     * Setter for minute part of expression.
     *
     * @param string $expression Minute part of expression
     *
     * @return Expression
     */
    public function setMinute($expression) {
        if (!$this->_minute) {
            $this->_minute = $this->_factory->createElement();
            $this->_minute->setMin(0);
            $this->_minute->setMax(59);
        }

        $this->_minute->setExpression($expression);

        return $this;
    }

    /**
     * Returns minute part of expression.
     *
     * @return string
     */
    public function getMinute() {
        return $this->_minute;
    }

    /**
     * Setter for hour part of expression.
     *
     * @param string $expression Hour part of expression
     *
     * @return Expression
     */
    public function setHour($expression) {
        if (!$this->_hour) {
            $this->_hour = $this->_factory->createElement();
            $this->_hour->setMin(0);
            $this->_hour->setMax(23);
        }

        $this->_hour->setExpression($expression);

        return $this;
    }

    /**
     * Returns hour part of expression.
     *
     * @return string
     */
    public function getHour() {
        return $this->_hour;
    }

    /**
     * Setter for day part of expression.
     *
     * @param string $expression Day part of expression
     *
     * @return Expression
     */
    public function setDay($expression) {
        if (!$this->_day) {
            $this->_day = $this->_factory->createElement();
            $this->_day->setMin(1);
            $this->_day->setMax(31);
        }

        $this->_day->setExpression($expression);

        return $this;
    }

    /**
     * Returns day part of expression.
     *
     * @return string
     */
    public function getDay() {
        return $this->_day;
    }

    /**
     * Setter for month part of expression.
     *
     * @param string $expression Month part of expression
     *
     * @return Expression
     */
    public function setMonth($expression) {
        if (!$this->_month) {
            $this->_month = $this->_factory->createElement();
            $this->_month->setMin(1);
            $this->_month->setMax(12);
        }

        $this->_month->setExpression($expression);

        return $this;
    }

    /**
     * Returns month part of expression.
     *
     * @return string
     */
    public function getMonth() {
        return $this->_month;
    }

    /**
     * Setter for day of week part of expression.
     *
     * @param string $expression Day of week part of expression
     *
     * @return Expression
     */
    public function setDayOfWeek($expression) {
        if (!$this->_dayOfWeek) {
            $this->_dayOfWeek = $this->_factory->createElement();
            $this->_dayOfWeek->setMin(0);
            $this->_dayOfWeek->setMax(7);
        }

        $this->_dayOfWeek->setExpression($expression);

        return $this;
    }

    /**
     * Returns day of week part of expression.
     *
     * @return string
     */
    public function getDayOfWeek() {
        return $this->_dayOfWeek;
    }

    /**
     * It parses all elements.
     *
     * @return Expression
     */
    public function parse() {
        $this->_minute->parse();
        $this->_hour->parse();
        $this->_day->parse();
        $this->_month->parse();
        $this->_dayOfWeek->parse();

        return $this;
    }

    /**
     * It parses expression string and set default values.
     *
     * @param string $expression Cron expression
     *
     * @return array
     */
    private function _parseExpression($expression) {
        $exploded = explode(' ', $expression);
        $result   = array(
            'minute'    => '*',
            'hour'      => '*',
            'day'       => '*',
            'month'     => '*',
            'dayOfWeek' => '*'
        );

        foreach ($exploded as $part) {
            $key          = key($result);
            $result[$key] = $part;
            next($result);
        }

        reset($result);

        return $result;
    }
}
