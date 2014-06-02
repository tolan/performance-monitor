<?php

namespace PF\Search\Filter\Target;

use PF\Main\Utils;
use PF\Search\Filter\Select;
use PF\Search\Enum\Format;

/**
 * This script defines abstract class for target entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
abstract class AbstractTarget {

    /**
     * List of types formats for each item attribute
     *
     * @var array
     */
    protected $_format = array();

    /**
     * List of default type. Each id will be converted to integer.
     *
     * @var array
     */
    private $_defaultFormat = array(
        'id' => Format::INT
    );

    /**
     * Utils instance
     *
     * @var \PF\Main\Utils
     */
    private $_utils = null;

    public function __construct(Utils $utils) {
        $this->_utils = $utils;
    }

    /**
     * Abstract method for define and set target table for entity.
     *
     * @param PF\Search\Filter\Select $select Select instance
     */
    abstract public function setTarget(Select $select);

    /**
     * Method for formating data. It takes data and converts each attribute by formating table.
     *
     * @param array $data Data
     *
     * @return array
     */
    public function format($data) {
        $format = array_merge($this->_defaultFormat, $this->_format);

        foreach ($data as $key => $item) {
            $data[$key] = $this->_formatItem($item, $format);
        }

        return $data;
    }

    /**
     * Method for formating item. It takes data and converts each attribute by formating table.
     *
     * @param array $item   Item of data
     * @param array $format Format table
     *
     * @return array
     */
    private function _formatItem($item, $format) {
        foreach ($item as $key => $value) {
            if (isset($format[$key])) {
                switch ($format[$key]) {
                    case Format::INT:
                        $item[$key] = (int)$value;
                        break;
                    case Format::FLOAT:
                        $item[$key] = (float)$value;
                        break;
                    case Format::DATETIME:
                        $item[$key] = $this->_utils->convertTimeFromMySQLDateTime($value);
                        break;
                }
            }
        }

        return $item;
    }
}
