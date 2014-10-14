<?php

namespace PF\Main;

/**
 * This script defines class for some helper tools.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Utils {
    const MYSQL_DATETIME = 'Y-m-d H:i:s';

    /**
     * It converts string to camel case format.
     *
     * @param string $string String to convert
     *
     * @return string
     */
    public function toCamelCase($string) {
        $str = str_replace(' ', '', ucwords(strtr($string, array('-' => ' ', '_' => ' '))));

        return lcfirst($str);
    }

    /**
     * Helper method for convert time to MYSQL datetime format.
     *
     * @param int $time Time in seconds
     *
     * @return string
     */
    public static function convertTimeToMySQLDateTime($time = null) {
        if ($time === null) {
            $time = time();
        }

        return date(self::MYSQL_DATETIME, $time);
    }

    /**
     * Helper method for convert time from MySQL datetime format.
     *
     * @param string  $datetime   MySQL datetime
     * @param boolean $forFclient Flag for convert it to microseconds (it use fclient)
     *
     * @return int
     */
    public static function convertTimeFromMySQLDateTime($datetime, $forFclient = true) {
        $time = $datetime === null ? $datetime : strtotime($datetime);

        if ($forFclient && $datetime !== null) {
            $time = $time * 1000;
        }

        return $time;
    }

    /**
     * Helper method for convert string of memory to bytes value.
     *
     * @param string $memoryString String in memory format
     *
     * @return int
     */
    public function convertMemory($memoryString) {
        $value = 0;
        if (stristr($memoryString, 'G')) {
            $value = (int)strstr($memoryString, 'G', true) * pow(2, 30); // There can be limit by 32-bit system
        } elseif (stristr($memoryString, 'M')) {
            $value = (int)strstr($memoryString, 'M', true) * pow(2, 20);
        } elseif (stristr($memoryString, 'K')) {
            $value = (int)strstr($memoryString, 'K', true) * pow(2, 10);
        } else {
            $value = (int)$memoryString;
        }

        return $value;
    }

    /**
     * Returns that the input array is associative.
     *
     * @param array $array Array
     *
     * @return boolean
     */
    public function isAssociativeArray(array $array) {
        return array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Converts string value to boolean.
     *
     * @param string $value Value
     *
     * @return boolean
     */
    public function convertToBoolean($value) {
        return $value !== 'false' && $value !== 0 && $value !== false;
    }
}
