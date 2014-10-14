<?php

namespace PF\Main\Logic\Evaluate\Databases;

use PF\Main\Logic\Evaluate\AbstractExtractor;

/**
 * This script defines class for database statement extractor for extracting input data for performer.
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
     * Return extracted scope data.
     *
     * @return \PF\Main\Database\Query
     */
    public function getScope() {
        $scope = $this->getEvaluator()->getScope();

        return $scope;
    }

    /**
     * Returns that scope was set into map.
     *
     * @return boolean
     */
    public function isSetScope() {
        return $this->getEvaluator()->getScope() !== null;
    }

    /**
     * Returns extracted data from original data.
     *
     * @param string $name Identificator of data
     *
     * @return \PF\Main\Database\Query
     */
    public function getData($name) {
        $data = $this->getEvaluator()->getData($name);

        return $data;
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
     * @return \PF\Main\Logic\Evaluate\Databases\Extractor
     */
    public function setIdentifier($identifier = 'id') {
        $this->_identifier = $identifier;

        return $this;
    }
}
