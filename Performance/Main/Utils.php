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
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));

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
        if (!$time) {
            $time = time();
        }

        return date(self::MYSQL_DATETIME, $time);
    }
}
