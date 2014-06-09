<?php

namespace PF\Main\System;

/**
 * This script defines class for manage system processes.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Process {

    /**
     * Returns ID of PHP program.
     *
     * @return int
     */
    public function getPid() {
        return getmypid();
    }

    /**
     * Returns usage of CPU for given process ID.
     *
     * @param int $pid Process ID
     *
     * @return float
     */
    public function cpuUsage($pid) {
        $proc = array();
        $exec = 'top -p '.$pid. ' -n 1 -b | grep '.$pid." | awk '{ print $9 }'";
        exec($exec, $proc);
        return (float)current($proc);
    }

    /**
     * Execute asynchronously program in command line.
     *
     * @param string $command Command for CLI
     *
     * @return \PF\Main\System\Process
     */
    public function exec($command) {
        exec($command.' >> /dev/null 2>&1 &');

        return $this;
    }

    /**
     * Kill process by given ID.
     *
     * @param int $pid Process ID
     * 
     * @return \PF\Main\System\Process
     */
    public function kill($pid) {
        exec('kill '.$pid);

        return $this;
    }
}
