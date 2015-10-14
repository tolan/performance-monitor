<?php

namespace PM\Main\System\Process;

use PM\Main\Abstracts\ArrayAccessIterator;

/**
 * This script defines entity class for store result of cli command execution.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Result extends ArrayAccessIterator {

    /**
     * Cli script command.
     *
     * @var string
     */
    private $_script = null;

    /**
     * Status about result.
     *
     * @var array
     */
    private $_status;

    /**
     * Returns executed script.
     *
     * @return string
     */
    public function getScript() {
        return $this->_script;
    }

    /**
     * Sets executed script.
     *
     * @param string $script Executed script
     *
     * @return Result
     */
    public function setScript($script) {
        $this->_script = $script;

        return $this;
    }

    /**
     * Returns result of executed command script.
     *
     * @return array
     */
    public function getResult() {
        return $this->_data;
    }

    /**
     * Sets result of executed command script.
     *
     * @param array $result Result of executed command
     *
     * @return Result
     */
    public function setResult($result) {
        $this->_data = $result;

        return $this;
    }

    /**
     * Sets status about executed command script.
     *
     * @param array $status Stataus about result
     *
     * @return Result
     */
    public function setStatus($status) {
        $this->_status = $status;

        return $this;
    }

    /**
     * Gets status about executed command script.
     *
     * @return array
     */
    public function getStatus() {
        return $this->_status;
    }

    /**
     * Gets count of returned lines.
     *
     * @return array
     */
    public function getCount() {
        return count($this->_data);
    }
}
