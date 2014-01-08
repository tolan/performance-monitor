<?php

namespace PF\Main\Abstracts;

/**
 * This script defines abstract class for enums.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Enum {

    /**
     * Storage for enums
     *
     * @var array
     */
    private static $_constants = array();

    /**
     * Returns all constants from enum.
     *
     * @return array
     */
    public static function getConstants() {
        $class = get_called_class();
        if (!isset(self::$_constants[$class])) {
            $refl = new \ReflectionClass($class);
            self::$_constants[$class] = $refl->getConstants();
        }

        return self::$_constants[$class];
    }
}
