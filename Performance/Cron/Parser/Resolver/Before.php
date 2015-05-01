<?php

namespace PM\Cron\Parser\Resolver;

use PM\Cron\Parser\Date;

/**
 * This script defines class for resolving date and time which is before datetime in expression instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Before extends AbstractResolver {

    /**
     * Returns datetime which is before datetime in expression.
     *
     * @return Parser\Date|null
     */
    public function getResult() {
        $expression = $this->getExpression();
        $expression->parse();

        $minute = $expression->getMinute()->getBefore(1);
        $hour   = $expression->getHour()->getBefore($minute > $expression->getMinute()->getActual());
        if ($hour != $expression->getHour()->getActual()) {
            $minute = $expression->getMinute()->getLast();
        }

        $dayOfMonth = $expression->getDay()->getBefore($hour > $expression->getHour()->getActual());
        $dayOfWeek  = $expression->getDayOfWeek()->getBefore($hour > $expression->getHour()->getActual());
        $day        = $this->_resolveBeforeDay($dayOfMonth, $dayOfWeek);
        if ($day !== $expression->getDay()->getActual()) {
            $minute = $expression->getMinute()->getLast();
            $hour   = $expression->getHour()->getLast();
        }

        $month = $expression->getMonth()->getBefore($day > $expression->getDay()->getActual());
        $year  = $expression->getDatetime()->format('Y');
        if ($month.'-'.$day.' '.$hour.':'.$minute > $expression->getDatetime()->format('m-d H:i')) {
            $year--;
        }

        if($month !== $expression->getMonth()->getActual() || $year !== $expression->getDatetime()->format('Y')) {
            for ($i = 0; $i < 12; $i++) {
                $day = $this->_resolveLastDayOfMonth($year, $month);
                if ($day !== null) {
                    break;
                }

                $sub   = -($month - $expression->getMonth()->getActual() - 1);
                $month = $expression->getMonth()->getBefore($sub);
            }

            $minute = $expression->getMinute()->getLast();
            $hour   = $expression->getHour()->getLast();
        }

        if (date('t', strtotime($year.'-'.$month)) < $day) {
            for ($i = 0; $i < 12; $i++) {
                $day = $this->_resolveLastDayOfMonth($year, $month);

                if (date('t', strtotime($year.'-'.$month)) < $day) {
                    $sub    = -($month - $expression->getMonth()->getActual() - 1);
                    $month  = $expression->getMonth()->getBefore($sub);
                } else {
                    $minute = $expression->getMinute()->getLast();
                    $hour   = $expression->getHour()->getLast();
                    break;
                }

                if ($month >= $expression->getMonth()->getActual()) {
                    $year = $expression->getDatetime()->format('Y') - 1;
                }
            }
        }

        if ($day < 0) {
            $sub    = -($month - $expression->getMonth()->getActual() - 1);
            $month  = $expression->getMonth()->getBefore($sub);
            $day    = date('t', strtotime($year.'-'.$month)) + $day;
        }

        return new Date($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00');
    }

    /**
     * Returns day of month which is before of day of month and day of week in arguments.
     *
     * @param int $dayOfMonth Day of month
     * @param int $dayOfWeek  Day of week
     *
     * @return string (day of month with leading zero)
     */
    private function _resolveBeforeDay($dayOfMonth, $dayOfWeek) {
        $expression = $this->getExpression();
        if ($expression->getDatetime()->format('w') < $dayOfWeek) {
            $dayOfWeek -= 7;
        }

        $isAllDaysOfWeek  = $expression->getDayOfWeek()->isFullRange();
        $isAllDaysOfMonth = $expression->getDay()->isFullRange();

        if ($isAllDaysOfWeek === false && $isAllDaysOfMonth === false) {
            $result = $dayOfMonth;
            if (($expression->getDatetime()->format('d') - $dayOfMonth) < ($expression->getDatetime()->format('w') - $dayOfWeek)) {
                $result = $expression->getDatetime()->format('d') - ($expression->getDatetime()->format('w') - $dayOfWeek);
            }
        } elseif ($isAllDaysOfMonth === true && $isAllDaysOfWeek === false) {
            $result = $expression->getDatetime()->format('d') - ($expression->getDatetime()->format('w') - $dayOfWeek);
        } elseif ($isAllDaysOfWeek === true) {
            $result = $dayOfMonth;
        }

        return str_pad($result, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns last day of the month and the year.
     *
     * @param int $year  Year
     * @param int $month Month
     *
     * @return string (Day of month with leading zero)
     */
    private function _resolveLastDayOfMonth($year, $month) {
        $expression       = $this->getExpression();
        $isAllDaysOfWeek  = $expression->getDayOfWeek()->isFullRange();
        $isAllDaysOfMonth = $expression->getDay()->isFullRange();
        $day              = null;

        $tmp = strtotime($year.'-'.$month.'-'.date('t', strtotime($year.'-'.$month))); // get last day
        if ($isAllDaysOfWeek === false && $isAllDaysOfMonth === false) {
            while(true) {
                if (in_array(date('w', $tmp), $expression->getDayOfWeek()->parse())) {
                    $day = date('d', $tmp);
                    break;
                }

                if (date('d', $tmp) == $expression->getDay()->getLast()) {
                    $day = date('d', $tmp);
                }

                $tmp = strtotime(date('Y-m-d', $tmp).' -1day');
            }
        } elseif ($isAllDaysOfMonth === true && $isAllDaysOfWeek === false) {
            while(true) {
                if (in_array(date('w', $tmp), $expression->getDayOfWeek()->parse())) {
                    $day = date('d', $tmp);
                    break;
                }

                $tmp = strtotime(date('Y-m-d', $tmp).' -1day');
            }
        } elseif ($isAllDaysOfWeek === true) {
            foreach (array_reverse($expression->getDay()->parse()) as $item) {
                if ($item <= date('d', $tmp)) {
                    $day = $item;
                    break;
                }
            }
        }

        return str_pad($day, 2, '0', STR_PAD_LEFT);
    }
}
