<?php

namespace PM\Main\System;

use PM\Main\Utils;

/**
 * This script defines class for manage system memory.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Memory {

    /**
     * Utils instance.
     *
     * @var \PM\Main\Utils
     */
    private $_utils;

    /**
     * Construct method.
     *
     * @param \PM\Main\Utils $utils Utils instance
     *
     * @return void
     */
    public function __construct(Utils $utils) {
        $this->_utils = $utils;
    }

    /**
     * Returns memory limit for PHP process in bytes.
     *
     * @return int
     */
    public function getLimit() {
        return $this->_utils->convertMemory(ini_get('memory_limit'));
    }

    /**
     * Returns consumed memory in bytes.
     *
     * @return int
     */
    public function getUsed() {
        return memory_get_usage(true);
    }

    /**
     * Returns free memory in bytes.
     *
     * @return int
     */
    public function getFree() {
        return $this->getLimit() - $this->getUsed();
    }

    /**
     * Returns used memory compared to memory limit. It is relative usage of memory.
     *
     * @return float It is between 0 to 1.
     */
    public function getRelativeUsage() {
        return $this->getUsed() / $this->getLimit();
    }

    /**
     * Returns peak of used memory in bytes.
     *
     * @return int
     */
    public function getPeak() {
        return memory_get_peak_usage(true);
    }
}
