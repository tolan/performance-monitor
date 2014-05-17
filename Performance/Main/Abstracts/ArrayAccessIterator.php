<?php

namespace PF\Main\Abstracts;

use PF\Main\Interfaces;

/**
 * Abstract class for array access iterator object.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class ArrayAccessIterator implements Interfaces\ArrayAccess, Interfaces\Iterator {

    /**
     * Array data
     *
     * @var array
     */
    protected $_data = array();

    /**
     * Returns whether a offset exists
     *
     * @param mixed $offset An offset to check for
     *
     * @return boolean
     */
    public function offsetExists ($offset) {
        return array_key_exists($offset, $this->_data);
    }

    /**
     * Returns value from array at offset position.
     *
     * @param mixed $offset The offset to retrieve
     *
     * @return mixed
     */
    public function offsetGet ($offset) {
        return $this->_data[$offset];
    }

    /**
     * Sets value to array at offset position.
     *
     * @param mixed $offset The offset to assign the value to
     * @param mixed $value  The value to set
     *
     * @return void
     */
    public function offsetSet ($offset, $value) {
        if ($offset === null) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    /**
     * Unsets value in array at offset position.
     *
     * @param mixed $offset The offset to unset
     *
     * @return void
     */
    public function offsetUnset ($offset) {
        unset($this->_data[$offset]);
    }

    /**
     * Returns the current element.
     *
     * @return mixed
     */
    public function current () {
        return current($this->_data);
    }

    /**
     * Move forward to next element.
     *
     * @return void
     */
    public function next () {
        return next($this->_data);
    }

    /**
     * Returns the key of the current element.
     *
     * @return mixed|null
     */
    public function key () {
        return key($this->_data);
    }

    /**
     * Checks if current position is valid.
     *
     * @return boolean
     */
    public function valid () {
        return key($this->_data) !== null;
    }

    /**
     * Rewind the Iterator to the first element.
     *
     * @return void
     */
    public function rewind () {
        return reset($this->_data);
    }

    /**
     * Shift current elemtent from array and return it.
     *
     * @return mixed
     */
    public function arrayShift() {
        $item = $this->current();
        unset($this->_data[$this->key()]);

        return $item;
    }

    /**
     * Prepend element to the beginning of array.
     *
     * @param mixed $value The value to prepend.
     *
     * @return int the new number of element in the array.
     */
    public function arrayUnshift($value) {
        return array_unshift($this->_data, $value);
    }

    /**
     * Returns array data.
     *
     * @return array
     */
    public function toArray() {
        return $this->_data;
    }

    /**
     * Loads data from array.
     *
     * @param array $array Data for load into array
     *
     * @return \PF\Main\Abstracts\ArrayAccessIterator
     */
    public function fromArray(array $array) {
        $this->_data = $array;

        return $this;
    }
}
