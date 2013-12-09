<?php

/**
 * Abstract class for entity object.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Abstract_Entity extends Performance_Main_Abstract_Enum {

    /**
     * @var array Variable for values
     */
    private $_data = array();

    /**
     * @var int Version of instance transfer
     */
    private $_version = 0;

    /**
     * Method for transforming from array.
     *
     * @param array $array Array with values.
     *
     * @return Performance_Main_Abstract_Entity
     */
    public function fromArray($array) {
        foreach ((array)$array as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * Returns transformed data into array
     *
     * @param boolean $recursive Flag for recursive call toArray
     *
     * @return array
     */
    public function toArray($recursive = true) {
        $result = array();
        $vars   = get_class_vars(get_class($this));

        foreach (array_keys($vars) as $name) {
            if (strpos($name, '_') !== 0) {
                $result[$name] = $this->_getItemValue($this->$name, $recursive);
            }
        }

        foreach ($this->_data as $key => $item) {
            $result[$key] = $this->_getItemValue($item, $recursive);
        }

        return $result;
    }

    /**
     * Returns transformed item to array or scalar.
     *
     * @param mixed   $item      Item of self object
     * @param boolean $recursive Flag for recursive call toArray.
     *
     * @return mixed
     */
    private function _getItemValue($item, $recursive = true) {
        if ($recursive == FALSE) {
            if (is_array($item)) {
                $tmp = array();
                foreach ($item as $value) {
                    $tmp[] = $this->_getItemValue($value, $recursive);
                }

                return $tmp;
            } else {
                return $item;
            }
        }

        if ($item instanceof IteratorAggregate || is_array($item)) {
            $tmp = array();
            foreach($item as $key => $val) {
                $tmp[$key] = $this->_getItemValue($val, $recursive);
            }

            return $tmp;
        } elseif ($item instanceof Entity) {
            return $item->toArray($recursive);
        } else {
            return $item;
        }

        return NULL;
    }

    /**
     * Returns version of state instance.
     *
     * @return int $version Version of entity
     */
    final public function getVersion() {
        return $this->_version;
    }

    /**
     * Geter for variable.
     *
     * @param string $name Name of variable.
     *
     * @return mixed Value of variable
     *
     * @throws Performance_Main_Exception Throws when parameter has not been set.
     */
    final public function get($name) {
        $name = lcfirst($name);

        if (!$this->has($name)) {
            throw new Performance_Main_Exception ('Parameter '.$name.' has not been set.');
        }

        $thisVars = get_class_vars(get_class($this));

        if (array_key_exists($name, $thisVars)) {
            $value = $this->$name;
        } else {
            $value = $this->_data[$name];
        }

        return $value;
    }

    /**
     * Seter for variable.
     *
     * @param string $name  Name of variable.
     * @param mixed  $value Vale of variable.
     *
     * @return Performance_Main_Abstract_Entity
     */
    final public function set($name, $value) {
        $thisVars = get_class_vars(get_class($this));
        $name     = lcfirst($name);

        if (array_key_exists($name, $thisVars)) {
            $this->$name = $value;
        } else {
            $this->_data[$name] = $value;
        }

        $this->_version++;

        return $this;
    }

    /**
     * Unset variable or explicited variable set to null.
     *
     * @param string $name Name of variable.
     *
     * @return Performance_Main_Abstract_Entity
     *
     * @throws Performance_Main_Exception Throws when parameter has not been set.
     */
    final public function reset($name) {
        $name = lcfirst($name);
        $vars = get_class_vars(get_class($this));

        if (array_key_exists($name, $vars)) {
            $this->$name = NULL;
        } elseif(array_key_exists($name, $this->_data)) {
            unset($this->_data[$name]);
        } else {
            throw new Performance_Main_Exception ('Variable with name: "'.$name.'" does not exist!');
        }

        return $this;
    }

    /**
     * Returns whether the variable exists.
     *
     * @param string $name Name of variable.
     *
     * @return boolean It is true when varaible is set.
     */
    final public function has($name) {
        $name = lcfirst($name);
        $vars = get_class_vars(get_class($this));

        return array_key_exists($name, $vars) || array_key_exists($name, $this->_data);
    }

    /**
     * Returns whether the variable is empty.
     *
     * @param string $name Name of variable.
     *
     * @return boolean It is true when variable is empty.
     *
     * @throws Performance_Main_Exception Throws when parameter has not been defined.
     */
    final public function isEmpty($name) {
        $vars = get_class_vars(get_class($this));
        $name = lcfirst($name);

        if (!$this->has($name)) {
            throw new Performance_Main_Exceptione ('Parameter "'.$name.'" has not been set.');
        }

        if (array_key_exists($name, $vars)) {
            return empty($this->$name);
        } else {
            return empty($this->_data[$name]);
        }
    }

    /**
     * Getter for value.
     *
     * @param string $name Name of value
     *
     * @return mixed
     *
     * @throws Performance_Main_Exception
     */
    final public function __get($name) {
        return $this->get($name);
    }

    /**
     * Setter for value.
     *
     * @param string $name  Name of value
     * @param mixed  $value Value of item
     *
     * @return Performance_Main_Abstract_Entity
     */
    final public function __set($name, $value) {
        return $this->set($name, $value);
    }

    /**
     * Calling methods with support fluent interface.
     *
     * @param string $name      Name of variable.
     * @param mixed  $arguments Arguments for methods.
     *
     * @return Performance_Main_Abstract_Entity | mixed | boolean
     *
     * @throws Performance_Main_Exception Throws when method is not defined.
     */
    final public function __call($name, $arguments) {
        $method   = strtolower(substr($name, 0, 3));
        $var      = substr($name, 3);

        if ($method == 'set') {
            return $this->set($var, current($arguments));
        }

        if ($method == 'get') {
            return $this->get($var);
        }

        if ($method == 'has') {
            return $this->has($var);
        }

        throw new Performance_Main_Exception ('Undefined method!');
    }

    /**
     * Proxy method for has.
     *
     * @param string $name Name of value.
     *
     * @return boolean
     */
    final public function __isset($name) {
        return $this->has($name);
    }

    /**
     * It unsets variable if exists.
     *
     * @param string $name Name of varible.
     *
     * @return Performance_Main_Abstract_Entity
     *
     * @throws Performance_Main_Exception Throw when variable does not exist.
     */
    final public function __unset($name) {
        return $this->reset($name);
    }
}