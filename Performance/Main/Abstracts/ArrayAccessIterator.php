<?php

namespace PF\Main\Abstracts;

use PF\Main\Interfaces;

abstract class ArrayAccessIterator implements Interfaces\ArrayAccess, Interfaces\Iterator {

    protected $_data = array();

    public function offsetExists ($offset) {
        return array_key_exists($offset, $this->_data);
    }

    public function offsetGet ($offset) {
        return $this->_data[$offset];
    }

    public function offsetSet ($offset, $value) {
        if ($offset === null) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetUnset ($offset) {
        unset($this->_data[$offset]);
    }

    public function current () {
        return current($this->_data);
    }

    public function next () {
        return next($this->_data);
    }

    public function key () {
        return key($this->_data);
    }

    public function valid () {
        return key($this->_data) !== null;
    }

    public function rewind () {
        return reset($this->_data);
    }

    public function arrayShift() {
        $item = $this->current();
        unset($this->_data[$this->key()]);

        return $item;
    }

    public function arrayUnshift($value) {
        return array_unshift($this->_data, $value);
    }

    public function toArray() {
        return $this->_data;
    }

    public function fromArray($array) {
        $this->_data = $array;

        return $this;
    }
}
