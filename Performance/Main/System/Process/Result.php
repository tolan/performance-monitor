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
}
