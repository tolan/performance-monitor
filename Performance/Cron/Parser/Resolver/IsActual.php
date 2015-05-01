<?php

namespace PM\Cron\Parser\Resolver;

use PM\Cron\Parser\Date;

/**
 * This script defines class for resolving date and time which is same as datetime in expression instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class IsActual extends AbstractResolver {

    /**
     * Returns datetime when datetime in expression is actual.
     *
     * @return Parser\Date|null
     */
    public function getResult() {
        $expression = $this->getExpression();

        $minute = $expression->getMinute()->isActual();
        $hour   = $expression->getHour()->isActual();
        $day    = $this->_isActualDay();
        $month  = $expression->getMonth()->isActual();

        $result = null;
        if ($minute && $hour && $day && $month) {
            $date   = $expression->getDatetime();
            $actual = $date->format('Y').'-'.$date->format('m').'-'.$date->format('d').' '.$date->format('H').':'.$date->format('i').':00';
            $result = new Date($actual);
        }

        return $result;
    }

    /**
     * Returns that day is actual of month or week.
     *
     * @return boolean
     */
    private function _isActualDay() {
        $expression = $this->getExpression();
        $result     = false;

        $isAllDaysOfWeek  = $expression->getDayOfWeek()->isFullRange();
        $isAllDaysOfMonth = $expression->getDay()->isFullRange();

        if ($isAllDaysOfMonth === true && $isAllDaysOfWeek === false) {
            $result = $expression->getDayOfWeek()->isActual();
        } elseif ($isAllDaysOfMonth === false && $isAllDaysOfWeek === true) {
            $result = $expression->getDay()->isActual();
        } else {
            $result = $expression->getDay()->isActual() || $expression->getDayOfWeek()->isActual();
        }

        return $result;
    }
}
