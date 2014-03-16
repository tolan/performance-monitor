<?php

namespace PF\Main\Cache;

abstract class AbstractCache implements Interfaces\Driver {

    protected $_data = array();

    public function load($name = null) {
        $data = array();
        if ($name === null) {
            $data = $this->_data;
        } elseif ($this->has($name)) {
            $data = $this->_data[$name];
        } else {
            throw new Exception('Undefined cache variable.');
        }

        return $data;
    }

    public function save($name, $value) {
        $this->_data[$name] = $value;

        return $this;
    }

    public function has($name) {
        return array_key_exists($name, $this->_data);
    }

    public function clean($name=null) {
        if ($name === null) {
            $this->_data = array();
        } else if (!$this->has($name)) {
            throw new Exception('Undefined cache variable.');
        } else {
            unset($this->_data[$name]);
        }

        return $this;
    }
}
