<?php

namespace PM\Settings\Gearman;

use PM\Main\System\Process;
use PM\Settings\Gearman\Status;

/**
 * This script defines class for managing opeartion above gearman scripts.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Settings
 */
class Operation {

    /**
     * Process instance
     *
     * @var Process
     */
    private $_process = null;

    /**
     * Gearman server status instance.
     *
     * @var Status
     */
    private $_status = null;

    /**
     * Construct method.
     *
     * @param Process $process Process instance
     * @param Status  $status  Gearman server status instance
     *
     * @return void
     */
    public function __construct(Process $process, Status $status) {
        $this->_process = $process;
        $this->_status  = $status;
    }

    /**
     * Starts gearman worker script by given worker options and actual status.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return boolean
     */
    public function start($status, $worker) {
        $script = $this->_getStartScript($status, $worker);

        $this->_process->execInBackground($script);

        return true;
    }

    /**
     * Stops one gearman worker script by given worker options and actual status.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return boolean
     */
    public function stop($status, $worker) {
        $script = 'ps ax | grep "'.$this->_getStartScript($status, $worker).'" | grep -v grep';
        $script = strtr($script, array('\\\\' => '\\\\\\'));
        $pids   = $this->_getRunningsPids($script);

        $this->_process->kill(current($pids));

        return true;
    }

    /**
     * Stops all gearman worker scripts by given worker options and actual status.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return boolean
     */
    public function stopAll($status, $worker) {
        $script = 'ps ax | grep "'.$this->_getStartScript($status, $worker).'" | grep -v grep';
        $script = strtr($script, array('\\\\' => '\\\\\\'));
        $pids   = $this->_getRunningsPids($script);

        foreach ($pids as $pid) {
            $this->_process->kill($pid);
        }

        return true;
    }

    /**
     * It keeps count of instances by status settings for worker.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return boolean
     */
    public function keep($status, $worker) {
        $statuses  = $this->_status->get($status['name']);
        $keepCount = $status['keepCount'];

        if (is_numeric($keepCount)) {
            foreach ($statuses as $stat) {
                $available = $stat['available'];

                if ($keepCount == 0) {
                    $this->stopAll($status, $worker);
                } elseif ($available < $keepCount) {
                    for ($available; $available < $keepCount; $available++) {
                        $this->start($status, $worker);
                    }
                } elseif ($available > $keepCount) {
                    for ($keepCount; $keepCount < $available; $keepCount++) {
                        $this->stop($status, $worker);
                    }
                }
            }
        }

        return true;
    }

    /**
     * Returns set of pids for script.
     *
     * @param string $script Script start string
     *
     * @return array
     */
    private function _getRunningsPids($script) {
        $processes = $this->_process->exec($script);
        $pids = array();
        foreach ($processes as $process) {
            $pid    = strstr(trim($process), ' ', true);
            $pids[] = $pid;
        }

        return $pids;
    }

    /**
     * Returns script start string with replaced placeholders.
     *
     * @param array $status Actual gearman worker status
     * @param array $worker Worker options
     *
     * @return string
     */
    private function _getStartScript($status, $worker) {
        $script = $worker['script'];

        $match = array();
        preg_match('/'.$worker['name'].'/', $status['name'], $match);

        foreach ($match as $key => $value) {
            $value  = strtr($value, array('\\' => '\\\\\\'));
            $script = preg_replace('/\$'.($key + 1).'/', $value, $script);
        }

        return $script;
    }
}
