<?php

namespace PF\Main\Interfaces;

/**
 * Interface for array access objects.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface ArrayAccess extends \ArrayAccess {

    /**
     * Shift current elemtent from array and return it.
     *
     * @return mixed
     */
    public function arrayShift();

    /**
     * Prepend element to the beginning of array.
     *
     * @param mixed $value The value to prepend.
     *
     * @return int the new number of element in the array.
     */
    public function arrayUnshift($value);

    /**
     * Returns array data.
     *
     * @return array
     */
    public function toArray();

    /**
     * Loads data from array.
     *
     * @param array $array Data for load into array
     *
     * @return \PF\Main\Abstracts\ArrayAccessIterator
     */
    public function fromArray(array $array);
}
