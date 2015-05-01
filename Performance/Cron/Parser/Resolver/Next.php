<?php

namespace PM\Cron\Parser\Resolver;

use PM\Cron\Parser\Date;

/**
 * This script defines class for resolving date and time which is after datetime in expression instance.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
class Next extends AbstractResolver {

    /**
     * Returns datetime which is after datetime in expression.
     *
     * @return Parser\Date|null
     */
    public function getResult() {
        $expression = $this->getExpression();
        $expression->parse();

        $minute = $expression->getMinute()->getNext(1);
        $hour   = $expression->getHour()->getNext($minute < $expression->getMinute()->getActual());
        if ($hour != $expression->getHour()->getActual()) {
            $minute = $expression->getMinute()->getFirst();
        }

        $dayOfMonth = $expression->getDay()->getNext($hour < $expression->getHour()->getActual());
        $dayOfWeek  = $expression->getDayOfWeek()->getNext($hour < $expression->getHour()->getActual());
        $day        = $this->_resolveNextDay($dayOfMonth, $dayOfWeek);
        if ($day !== $expression->getDay()->getActual()) {
            $minute = $expression->getMinute()->getFirst();
            $hour   = $expression->getHour()->getFirst();
        }

        $month = $expression->getMonth()->getNext($day < $expression->getDay()->getActual());
        $year  = $expression->getDatetime()->format('Y');
        if ($month < $expression->getMonth()->getActual()) {
            $year++;
        }

        if($month !== $expression->getMonth()->getActual() || $year !== $expression->getDatetime()->format('Y')) {
            for ($i = 0; $i < 12; $i++) {
                $day = $this->_resolveFirstDayOfMonth($year, $month);
                if ($day !== null) {
                    break;
                }

                $add   = -($expression->getMonth()->getActual() - $month + 1);
                $month = $expression->getMonth()->getNext($add);
            }

            $minute = $expression->getMinute()->getFirst();
            $hour   = $expression->getHour()->getFirst();
        }

        for ($i = 0; $i < 12 && (date('t', strtotime($year.'-'.$month.'-01')) < $day); $i++) {
            $add   = -($expression->getMonth()->getActual() - $month);
            $month = $expression->getMonth()->getNext($add + 1);
            $day   = $this->_resolveFirstDayOfMonth($year, $month);
            $minute = $expression->getMinute()->getFirst();
            $hour   = $expression->getHour()->getFirst();

            if ($month <= $expression->getMonth()->getActual()) {
                $year = $expression->getDatetime()->format('Y') + 1;
            }
        }

        return new Date($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':00');
    }

    /**
     * Returns day of month which is next of day of month and day of week in arguments.
     *
     * @param int $dayOfMonth Day of month
     * @param int $dayOfWeek  Day of week
     *
     * @return string (Day of month with leading zero)
     */
    private function _resolveNextDay($dayOfMonth, $dayOfWeek) {
        $expression = $this->getExpression();
        if ($expression->getDatetime()->format('w') > $dayOfWeek) {
            $dayOfWeek += 7;
        }

        $isAllDaysOfWeek  = $expression->getDayOfWeek()->isFullRange();
        $isAllDaysOfMonth = $expression->getDay()->isFullRange();

        if ($isAllDaysOfWeek === false && $isAllDaysOfMonth === false) {
            $result = $dayOfMonth;
            if (($dayOfMonth - $expression->getDatetime()->format('d')) > ($dayOfWeek - $expression->getDatetime()->format('w'))) {
                $result = $expression->getDatetime()->format('d') + ($dayOfWeek - $expression->getDatetime()->format('w'));
            }
        } elseif ($isAllDaysOfMonth === true && $isAllDaysOfWeek === false) {
            $result = $expression->getDatetime()->format('d') + ($dayOfWeek - $expression->getDatetime()->format('w'));
        } elseif ($isAllDaysOfWeek === true) {
            $result = $dayOfMonth;
        }

        return str_pad($result, 2, '0', STR_PAD_LEFT);
    }

    /**
     * Returns first day of the month and the year.
     *
     * @param int $year  Year
     * @param int $month Month
     *
     * @return string (Day of month with leading zero)
     */
    private function _resolveFirstDayOfMonth($year, $month) {
        $expression       = $this->getExpression();
        $isAllDaysOfWeek  = $expression->getDayOfWeek()->isFullRange();
        $isAllDaysOfMonth = $expression->getDay()->isFullRange();
        $day              = null;

        if ($isAllDaysOfWeek === false && $isAllDaysOfMonth === false) {
            $tmp = strtotime($year.'-'.$month.'-01');
            while(true) {
                if (in_array(date('w', $tmp), $expression->getDayOfWeek()->parse())) {
                    $day = date('d', $tmp);
                    break;
                }

                if (date('d', $tmp) == $expression->getDay()->getFirst()) {
                    $day = date('d', $tmp);
                }

                $tmp = strtotime(date('Y-m-d', $tmp).' +1day');
            }
        } elseif ($isAllDaysOfMonth === true && $isAllDaysOfWeek === false) {
            $tmp = strtotime($year.'-'.$month.'-01');
            while(true) {
                if (in_array(date('w', $tmp), $expression->getDayOfWeek()->parse())) {
                    $day = date('d', $tmp);
                    break;
                }

                $tmp = strtotime(date('Y-m-d', $tmp).' +1day');
            }
        } elseif ($isAllDaysOfWeek === true) {
            $day = $expression->getDay()->getFirst();
        }

        return str_pad($day, 2, '0', STR_PAD_LEFT);
    }
}
