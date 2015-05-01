<?php

namespace PM\Cron\Execution\Context;

use PM\Cron\Parser\Date;

/**
 * This script defines abstract class for execution context entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
 */
abstract class AbstractEntity {

    /**
     * ID of entity.
     * 
     * @var int
     */
    private $_id = null;

    /**
     * Date of execution start.
     * 
     * @var Date
     */
    private $_date = null;

    /**
     * Setter for ID.
     * 
     * @param int $id ID of entity
     * ¨
     * @return AbstractEntity
     */
    public function setId($id) {
        $this->_id = $id;

        return $this;
    }

    /**
     * Getter for ID.
     * 
     * @return int
     */
    public function getId() {
        return $this->_id;
    }

    /**
     * Setter for execution start date.
     * 
     * @param Date $date Execution start date
     * 
     * @return AbstractEntity
     */
    public function setDate(Date $date) {
        $this->_date = $date;

        return $this;
    }

    /**
     * Getter for execution start date.
     * 
     * @return Date
     */
    public function getDate() {
        return $this->_date;
    }
}
