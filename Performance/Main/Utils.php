<?php

namespace PM\Main;

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
     * @return string
     */
    public function convertMemory($memoryString) {
        $types   = array('P', 'T', 'G', 'M', 'K');
        $pattern = '/(\d+[.,]*\d*) *(['.join('', $types).'])/';
        $matches = array();

        preg_match_all($pattern, $memoryString, $matches);

        foreach ($matches[0] as $key => $match) {
            $amount   = strtr($matches[1][$key], array(',' => '.'));
            $type     = $matches[2][$key];
            $exponent = (count($types) - array_search($type, $types)) * 10;

            $value = round($amount * pow(2, $exponent), 0);

            $replace = strpos($match, ' ') ? $value.' ' : $value;

            $memoryString = str_replace($match, $replace, $memoryString);
        }

        return $memoryString;
    }

    /**
     * Returns that the input array is associative.
     *
     * @param array $array Array
     *
     * @return boolean
     */
    public function isAssociativeArray(array $array) {
        return !empty($array) && array_keys($array) !== range(0, count($array) - 1);
    }

    /**
     * Converts string value to boolean.
     *
     * @param string $value Value
     *
     * @return boolean
     */
    public function convertToBoolean($value) {
        return $value !== 'false' && $value !== 0 && $value !== false && $value !== '' && $value !== '0';
    }

    /**
     * Return short name of class.
     *
     * @param string|object $class Class to resolve the name
     *
     * @return string
     */
    public function getShortName($class) {
        if (is_object($class)) {
            $class = get_class($class);
        }

        $function = new \ReflectionClass($class);

        return $function->getShortName();
    }
}
