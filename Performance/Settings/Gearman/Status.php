<?php

namespace PM\Settings\Gearman;

use PM\Main\System\Process;

/**
 * This script defines class for get actual state of gearman server.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Status {

    /**
     * Command for get actual state of gearman server.
     */
    const COMMAND = 'gearadmin --status';

    /**
     * Set of keys for states.
     *
     * @var array
     */
    private static $_keys = array(
        'name',
        'queue',
        'running',
        'available'
    );

    /**
     * System process instance.
     *
     * @var Process
     */
    private $_process;

    /**
     * Construct method.
     *
     * @param Process $process Process instance
     *
     * @return void
     */
    public function __construct(Process $process) {
        $this->_process = $process;
    }

    /**
     * Returns actual status of gearman server.
     *
     * @param string $name Name of gearman worker
     *
     * @return array
     */
    public function get($name = null) {
        $status = $this->_process->exec(self::COMMAND);

        $statuses = $this->_parseStatus($status);
        $result   = array();
        if ($name !== null) {
            foreach ($statuses as $stat) {
                if ($stat['name'] === $name) {
                    $result[] = $stat;
                }
            }
        } else {
            $result = $statuses;
        }

        return $result;
    }

    /**
     * It returns parsed actual status from execution of command.
     *
     * @param Process\Result $status Status result from process
     *
     * @return array
     */
    private function _parseStatus(Process\Result $status) {
        $result = array();
        $count  = count($status);

        for($i = 0; $i < $count; $i++) {
            $row = $status[$i];

            $result[] = $this->_parseRow(trim($row));
        }

        return $result;
    }

    /**
     * It returns parsed actual status from one row of command result.
     *
     * @param string $row One row of command result
     *
     * @return array
     */
    private function _parseRow($row) {
        $result = array();

        preg_match('/^([a-zA-Z0-9_]+)\s+(\d+)\s+(\d+)\s+(\d+)$/', $row, $result);
        array_shift($result);

        $result = array_combine(self::$_keys, $result);

        foreach ($result as $key => $value) {
            if ($key !== 'name') {
                $result[$key] = (int)$value;
            }
        }

        return $result;
    }
}
