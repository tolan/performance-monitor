<?php

namespace PF\Main\Logic\Evaluate\Databases;

use PF\Main\Logic\Evaluate\AbstractPerformer;
use PF\Main\Database;

/**
 * This script defines class for database statement performer for evaluate expression and data.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performer extends AbstractPerformer {

    /**
     * Database instance
     *
     * @var \PF\Main\Database
     */
    private $_database;

    /**
     * Extractor instance
     *
     * @var \PF\Main\Logic\Evaluate\Databases\Extractor
     */
    private $_extractor;

    /**
     * Construct method for inject database instance.
     *
     * @param Database $database Database instance
     *
     * @return void
     */
    public function __construct(Database $database) {
        $this->_database = $database;

        $this->_extractor = new Extractor();
    }

    /**
     * Returns relevant extractor.
     *
     * @return \PF\Main\Logic\Evaluate\Databases\Extractor
     */
    protected function createExtractor() {
        return $this->_extractor;
    }

    /**
     * Returns relevant composer.
     *
     * @return \PF\Main\Logic\Evaluate\Databases\Composer
     */
    protected function createComposer() {
        return new Composer();
    }

    /**
     * This method evaluate operator AND.
     *
     * @param \PF\Main\Database\Select $first  First select
     * @param \PF\Main\Database\Select $second Second select
     *
     * @return \PF\Main\Database\Select
     */
    protected function perform_and($first, $second) {
        $identifier = $this->_extractor->getIdentifier();
        $select     = clone $this->_extractor->getScope(); /* @var $select \PF\Main\Database\Select */

        $select->where($identifier.' IN (?)', $first);
        $select->where($identifier.' IN (?)', $second);

        return $select;
    }

    /**
     * This method evaluate operator OR.
     *
     * @param \PF\Main\Database\Select $first  First select
     * @param \PF\Main\Database\Select $second Second select
     *
     * @return \PF\Main\Database\Select
     */
    protected function perform_or($first, $second) {
        $identifier = $this->_extractor->getIdentifier();
        $select     = clone $this->_extractor->getScope(); /* @var $select \PF\Main\Database\Select */

        $select->where($identifier.' IN (?)', $first);
        $select->orWhere($identifier.' IN (?)', $second);

        return $select;
    }

    /**
     * This method evaluate operator NAND.
     *
     * @param \PF\Main\Database\Select $first  First select
     * @param \PF\Main\Database\Select $second Second select
     *
     * @return \PF\Main\Database\Select
     */
    protected function perform_nand($first, $second) {
        $identifier = $this->_extractor->getIdentifier();
        $select     = clone $this->_extractor->getScope(); /* @var $select \PF\Main\Database\Select */

        $select->where($identifier.' NOT IN (?)', $first);
        $select->orWhere($identifier.' NOT IN (?)', $second);

        return $select;
    }

    /**
     * This method evaluate operator NOR.
     *
     * @param \PF\Main\Database\Select $first  First select
     * @param \PF\Main\Database\Select $second Second select
     *
     * @return \PF\Main\Database\Select
     */
    protected function perform_nor($first, $second) {
        $identifier = $this->_extractor->getIdentifier();
        $select     = clone $this->_extractor->getScope(); /* @var $select \PF\Main\Database\Select */

        $select->where($identifier.' NOT IN (?)', $first);
        $select->where($identifier.' NOT IN (?)', $second);

        return $select;
    }

    /**
     * This method evaluate operator XOR.
     *
     * @param \PF\Main\Database\Select $first  First select
     * @param \PF\Main\Database\Select $second Second select
     *
     * @return \PF\Main\Database\Select
     */
    protected function perform_xor($first, $second) {
        $identifier = $this->_extractor->getIdentifier();
        $select     = clone $this->_extractor->getScope(); /* @var $select \PF\Main\Database\Select */

        $where = $select->createWhere();
        $where->where($identifier.' NOT IN (?)', $first);
        $where->where($identifier.' IN (?)', $first);

        $orWhere = $select->createWhere();
        $orWhere->where($identifier.' IN (?)', $first);
        $orWhere->where($identifier.' NOT IN (?)', $first);

        $select->where($where);
        $select->orWhere($second);

        return $select;
    }

    /**
     * This method evaluate operator XNOR.
     *
     * @param \PF\Main\Database\Select $first  First select
     * @param \PF\Main\Database\Select $second Second select
     *
     * @return \PF\Main\Database\Select
     */
    protected function perform_xnor($first, $second) {
        $identifier = $this->_extractor->getIdentifier();
        $select     = clone $this->_extractor->getScope(); /* @var $select \PF\Main\Database\Select */

        $where = $select->createWhere();
        $where->where($identifier.' IN (?)', $first);
        $where->where($identifier.' IN (?)', $first);

        $orWhere = $select->createWhere();
        $orWhere->where($identifier.' NOT IN (?)', $first);
        $orWhere->where($identifier.' NOT IN (?)', $first);

        $select->where($where);
        $select->orWhere($second);

        return $select;
    }
}
