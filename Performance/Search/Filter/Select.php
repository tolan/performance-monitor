<?php

namespace PM\Search\Filter;

use PM\Main\Database;

/**
 * This script defines class for statement which extends standard database select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
 */
class Select extends Database\Select {

    /**
     * Helper variable for create unique name.
     *
     * @var int
     */
    private $_version = 0;

    /**
     * Return unique name for this select.
     *
     * @param string $prefix Prefix for name [optional]
     *
     * @return string
     */
    public function getUniqueTableAlias($prefix = 'data') {
        $this->_version++;

        return $prefix.'_'.$this->_version;
    }
}
