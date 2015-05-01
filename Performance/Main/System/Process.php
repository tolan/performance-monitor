<?php

namespace PM\Main\System;

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
     * Execute program in command line and returns result.
     *
     * @param string $command Command for CLI
     *
     * @return Process\Result
     */
    public function exec($command) {
        $ans = array();
        exec($command, $ans);

        $result = new Process\Result();
        $result->setScript($command);
        $result->setResult($ans);

        return $result;
    }

    /**
     * Execute asynchronously program in command line.
     *
     * @param string $command Command for CLI
     *
     * @return Process
     */
    public function execInBackground($command) {
        exec($command.' >> /dev/null 2>&1 &');

        return $this;
    }

    /**
     * Kill process by given ID.
     *
     * @param int $pid Process ID
     *
     * @return \PM\Main\System\Process
     */
    public function kill($pid) {
        exec('kill '.$pid);

        return $this;
    }
}
