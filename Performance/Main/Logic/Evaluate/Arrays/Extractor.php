<?php

namespace PM\Main\Logic\Evaluate\Arrays;

use PM\Main\Logic\Evaluate\AbstractExtractor;
use PM\Main\Logic\Exception;

/**
 * This script defines class for array extractor for extracting input data for performer.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Extractor extends AbstractExtractor {

    /**
     * Default indetifier of data.
     *
     * @var mixed
     */
    private $_identifier = 'id';

    /**
     * Map of extracted data and original data.
     *
     * @var array
     */
    private $_map = array();

    /**
     * Identifier for scope in map;
     *
     * @var string
     */
    private $_scopeIdentifier = 'scope';

    /**
     * Construct method.
     *
     * @return void
     */
    public function __construct() {
        $this->_scopeIdentifier = 'scope'.uniqid();
    }

    /**
     * Return extracted scope data.
     *
     * @return array
     */
    public function getScope() {
        $scope = $this->getEvaluator()->getScope();

        return $this->_extract($scope, $this->_scopeIdentifier);
    }

    /**
     * Returns that scope was set into map.
     *
     * @return boolean
     */
    public function isSetScope() {
        return array_key_exists($this->_scopeIdentifier, $this->_map);
    }

    /**
     * Returns extracted data from original data.
     *
     * @param string $name Identificator of data
     *
     * @return array
     */
    public function getData($name) {
        $data = $this->getEvaluator()->getData($name);

        return $this->_extract($data, $name);
    }

    /**
     * Returns map for extracted data to original data.
     *
     * @param string $name Data identificator
     *
     * @return array
     *
     * @throws \PM\Main\Logic\Exception Throws when data name is not set
     */
    public function getMap($name = null) {
        $map = $this->_map;

        if ($name !== null) {
            if (array_key_exists($name, $map) === false) {
                throw new Exception('Map for "'.$name.'" is not set.');
            }

            $map = $this->_map[$name];
        }

        return $map;
    }

    /**
     * Returns identifier of data.
     *
     * @return mixed
     */
    public function getIdentifier() {
        return $this->_identifier;
    }

    /**
     * Sets identifier for data (what parameter is identificator for evaluate).
     *
     * @param mixed $identifier Parameter identifier od data
     *
     * @return \PM\Main\Logic\Evaluate\Arrays\Extractor
     */
    public function setIdentifier($identifier) {
        $this->_identifier = $identifier;

        return $this;
    }

    /**
     * This method extracts data from original data and makes caching map.
     *
     * @param array  $data Original data
     * @param string $name Data identificator
     *
     * @return array
     *
     * @throws \PM\Main\Logic\Exception Throws when indetifier is undefined.
     */
    private function _extract($data = array(), $name = 'undefined') {
        $result = array();

        if (array_key_exists($name, $this->_map) === false) {
            $this->_map[$name] = array();
        }

        foreach ($data as $key => $item) {
            if (!isset($item[$this->getIdentifier()])) {
                throw new Exception('Undefined identifier.');
            }

            $identifier          = $item[$this->getIdentifier()];
            $result[$identifier] = array($name => $name);

            if (array_key_exists($identifier, $this->_map[$name]) === false) {
                $this->_map[$name][$identifier] = $key;
            }
        }

        return $result;
    }
}
