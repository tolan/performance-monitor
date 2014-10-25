<?php

namespace PM\Main\Abstracts;

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
     * Storage for selection.
     *
     * @var array
     */
    private static $_selection = array();

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

    /**
     * Returns list of constants in structure for fclient html select.
     *
     * @param string $textPrefix Prefix for text (e.g. "main.")
     *
     * @return array
     */
    public static function getSelection($textPrefix = 'main.') {
        $class = get_called_class();
        if (!isset(self::$_selection[$class])) {
            $constants = self::getConstants();
            $result    = array();

            foreach ($constants as $constant) {
                $result[] = array(
                    'value' => $constant,
                    'text'  => $textPrefix.$constant
                );
            }

            self::$_selection[$class] = $result;
        }

        return self::$_selection[$class];
    }
}
