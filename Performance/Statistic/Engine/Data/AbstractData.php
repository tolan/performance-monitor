<?php

namespace PM\Statistic\Engine\Data;

use PM\Statistic\Engine;

/**
 * This script defines abstract class for assign data condition to statistic select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
abstract class AbstractData {

    /**
     * Junction helper instance.
     *
     * @var Helper\Junction
     */
    private $_junctionHelper;

    /**
     * Construct method.
     *
     * @param \PM\Statistic\Engine\Helper\Junction $junctionHelper Junction helper instance
     *
     * @return void
     */
    public function __construct(Engine\Helper\Junction $junctionHelper) {
        $this->_junctionHelper = $junctionHelper;
    }

    /**
     * It adds data condition into statistic select.
     *
     * @param Engine\Select                     $select   Statistic select instance
     * @param Engine\Functions\AbstractFunction $function Statistic function instance
     * @param string                            $method   One of enum \PM\Statistic\Enum\View\Data
     *
     * @return Engine\Select
     *
     * @throws Exception Throws when method is not defined
     */
    public function addData(Engine\Select $select, Engine\Functions\AbstractFunction $function, $method) {
        if (method_exists($this, $method) === false) {
            throw new Exception('Method ('.$method.') is not defined.');
        }

        $this->$method($select, $function);

        return $select;
    }

    /**
     * Abstract method for time condition. It is for statistic which is based on time.
     *
     * @return Engine\Select
     */
    abstract public function addTime(Engine\Select $select);

    /**
     * Helper method for create junction to destination entity.
     *
     * @param Engine\Select $select      Statistic select instance
     * @param string        $destination One of \PM\Statistic\Enum\Source\Target
     *
     * @return AbstractData
     */
    protected function createJunction(Engine\Select $select, $destination) {
        $class  = get_called_class();
        $source = strtolower(substr($class, strrpos($class, '\\') + 1));

        $this->_junctionHelper->createJunctions($select, $source, $destination);

        return $this;
    }
}
