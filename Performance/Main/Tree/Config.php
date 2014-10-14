<?php

namespace PF\Main\Tree;

/**
 * This script defines class for settings configuration of tree structure.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Config {

    /**
     * Parameter identifier for entity.
     *
     * @var string
     */
    private $_id = 'id';

    /**
     * Parameter identifier for parent identifier.
     *
     * @var string
     */
    private $_parentId = 'parentId';

    /**
     * Paremeter identifier for define order in structure.
     *
     * @var string
     */
    private $_order = 'order';

    /**
     * Parameter identifier which is used for children in tree structure.
     *
     * @var string
     */
    private $_children = 'children';

    /**
     * Returns parameter identifier for entity.
     *
     * @return string
     */
    public function getIdentificator() {
        return $this->_id;
    }

    /**
     * Sets parameter identifier for entity.
     *
     * @param string $identificator Parameter identifier
     *
     * @return \PF\Main\Tree\Config
     */
    public function setIdentificator($identificator) {
        $this->_id = $identificator;

        return $this;
    }

    /**
     * Returtns parameter identifier for parent identifier.
     *
     * @return string
     */
    public function getParentIdentificator() {
        return $this->_parentId;
    }

    /**
     * Sets parameter identifier for parent identifier.
     *
     * @param string $identificator Parameter identifier for parent identifier.
     *
     * @return \PF\Main\Tree\Config
     */
    public function setParentIdentificator($identificator) {
        $this->_parentId = $identificator;

        return $this;
    }

    /**
     * Returns paremeter identifier for define order in structure.
     *
     * @return string
     */
    public function getOrderIdentificator() {
        return $this->_order;
    }

    /**
     * Sets paremeter identifier for define order in structure.
     *
     * @param string $identificator Paremeter identifier for define order in structure.
     *
     * @return \PF\Main\Tree\Config
     */
    public function setOrderIdentificator($identificator) {
        $this->_order = $identificator;

        return $this;
    }

    /**
     * Returns parameter identifier which is used for children in tree structure.
     *
     * @return string
     */
    public function getChildrenIdentificator() {
        return $this->_children;
    }

    /**
     * Sets parameter identifier which is used for children in tree structure.
     *
     * @param string $identificator Parameter identifier which is used for children in tree structure.
     *
     * @return \PF\Main\Tree\Config
     */
    public function setChildrenIdentificator($identificator) {
        $this->_children = $identificator;

        return $this;
    }

    /**
     * It checks that configuration is valid.
     *
     * @return \PF\Main\Tree\Config
     *
     * @throws Exception Throws when configuration is not valid (at least one parameter is empty).
     */
    public function validate() {
        if (empty($this->_id) || empty($this->_order) || empty($this->_parentId) || empty($this->_children)) {
            throw new Exception('Config is invalid.');
        }

        return $this;
    }
}
