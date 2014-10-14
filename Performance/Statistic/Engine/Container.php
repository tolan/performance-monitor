<?php

namespace PF\Statistic\Engine;

use PF\Main\Database;
use PF\Main\Log;
use PF\Search;

/**
 * This script defines class for storing statistic view lines, source and target selects for building into one SQL statement.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Container {

    /**
     * Helper for create junction instance.
     *
     * @var Helper\Junction
     */
    private $_junctionHelper = null;

    /**
     * Statistic select instance.
     *
     * @var Select
     */
    private $_select = null;

    /**
     * Flag whether select is compiled. Select can not be compiled twice.
     *
     * @var boolean
     */
    private $_compiled = false;

    /**
     * Name of source entity (entity of search).
     *
     * @var string
     */
    private $_sourceEntity = null;

    /**
     * Name of target entity (entity of view).
     *
     * @var string
     */
    private $_targetEntity = null;

    /**
     * Target select decorator instance
     *
     * @var Target\AbstractTarget
     */
    private $_target = null;

    /**
     * Source select with source entities.
     *
     * @var Search\Filter\Select
     */
    private $_source = null;

    /**
     * List of lines data.
     *
     * @var array
     */
    private $_lines = array();

    /**
     * Construct method.
     *
     * @param Database        $database       Database instance
     * @param Log             $log            Logger instance
     * @param Helper\Junction $junctionHelper Junction helper instance
     *
     * @return void
     */
    public function __construct(Database $database, Log $log, Helper\Junction $junctionHelper) {
        $this->_junctionHelper = $junctionHelper;
        $this->_select         = new Select($database->getConnection(), $log);
    }

    /**
     * Sets source entity and source select.
     *
     * @param string          $entity       One of enum \PF\Statistic\Enum\Source\Target
     * @param Database\Select $sourceSelect Source select instance
     *
     * @return Container
     */
    public function setSource($entity, Database\Select $sourceSelect) {
        $this->_validateCompiled();
        $this->_sourceEntity = $entity;
        $this->_source       = $sourceSelect;

        return $this;
    }

    /**
     * Sets target entity and target setter.
     *
     * @param string                $entity One of enum \PF\Statistic\Enum\Source\Target
     * @param Target\AbstractTarget $target Target setter instance
     *
     * @return Container
     */
    public function setTarget($entity, Target\AbstractTarget $target) {
        $this->_validateCompiled();
        $this->_targetEntity = $entity;
        $this->_target       = $target;

        return $this;
    }

    /**
     * It adds data condition and condition function.
     *
     * @param Data\AbstractData          $data     Data condition intance
     * @param Functions\AbstractFunction $function Condition function instance \PF\Statistic\Enum
     * @param string                     $method   One of enum \PF\Statistic\Enum\View\Data
     *
     * @return Container
     */
    public function addLine(Data\AbstractData $data, Functions\AbstractFunction $function, $method) {
        $this->_validateCompiled();
        $this->_lines[] = array(
            'data'     => $data,
            'function' => $function,
            'method'   => $method
        );

        return $this;
    }

    /**
     * Returns compiled select instance.
     *
     * @return Select
     */
    public function getSelect() {
        $this->_compile();

        return $this->_select;
    }

    /**
     * Returns all fetched data from compiled select.
     *
     * @return array
     */
    public function fetchAll() {
        $this->_compile();

        return $this->_select->fetchAll();
    }

    /**
     * It validates and compiles statistic select.
     *
     * @return Container
     */
    private function _compile() {
        if ($this->_compiled === false) {
            $this->_validateData();

            $this->_createTarget();
            $this->_compileJunctions();
            $this->_compileSource();
            $this->_compileLines();

            $this->_compiled = true;
        }

        return $this;
    }

    /**
     * It creates target entity in statistic select.
     *
     * @return Container
     */
    private function _createTarget() {
        $this->_target->setTarget($this->_select);

        return $this;
    }

    /**
     * It creates junctions between source and target entity.
     *
     * @return Container
     */
    private function _compileJunctions() {
        $this->_junctionHelper->createJunctions($this->_select, $this->_targetEntity, $this->_sourceEntity);

        return $this;
    }

    /**
     * It assigns source select into statistic select.
     *
     * @return Container
     */
    private function _compileSource() {
        $this->_junctionHelper->assignSource($this->_select, $this->_source, $this->_sourceEntity);

        return $this;
    }

    /**
     * It adds data of statistic view line into statistic select.
     *
     * @return Container
     */
    private function _compileLines() {
        foreach ($this->_lines as $line) {
            $function = $line['function']; /* @var $function Functions\AbstractFunction */
            $method   = $line['method'];
            $data     = $line['data']; /* @var $line Data\AbstractData */
            $data->addData($this->_select, $function, $method);
        }

        return $this;
    }

    /**
     * It validates that container is already compiled.
     *
     * @return Container
     *
     * @throws Exception Throws when container has been compiled.
     */
    private function _validateCompiled() {
        if ($this->_compiled === true) {
            throw new Exception('Container has been compiled.');
        }

        return $this;
    }

    /**
     * It validates data for compilation.
     *
     * @return Container
     *
     * @throws Exception Throws when data is not valid.
     */
    private function _validateData() {
        $isValid = true;

        $isValid = $isValid && $this->_sourceEntity !== null;
        $isValid = $isValid && $this->_targetEntity !== null;
        $isValid = $isValid && $this->_target !== null;
        $isValid = $isValid && $this->_source !== null;
        $isValid = $isValid && count($this->_lines) > 0;

        if (!$isValid) {
            throw new Exception('Statistic container has invalid data');
        }

        return $this;
    }
}
