<?php

namespace PM\Profiler\Monitor\Repository;

use PM\Main\Interfaces\Observable;
use PM\Main\Interfaces\Observer;
use PM\Main\Traits;

use PM\Profiler\Monitor\Interfaces\Storage;
use PM\Profiler\Monitor\Interfaces\Call;
use PM\Profiler\Monitor;

/**
 * This script defines class for monitor repository working with MySQL database.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class MySQL extends AbstractRepository implements Observer, Observable {

    use Traits\Observable;

    /**
     * table name for measures
     */
    const MEASURE_TABLE = 'test_measure';

    /**
     * table name for measure statistics
     */
    const STATS_TABLE = 'measure_statistic_data';

    /**
     * ID of measure
     *
     * @var int
     */
    private $_measureId = null;

    /**
     * Instance of monitor call fly weight
     *
     * @var \PM\Profiler\Monitor\Interfaces\Call
     */
    private $_call = null;

    /**
     * Init method for set default table name.
     *
     * @return void
     */
    public function init() {
        parent::init(self::MEASURE_TABLE);
    }

    /**
     * Method for update observer. It is called when storage changed state.
     *
     * @param \PM\Main\Interfaces\Observable $storage Monitor storage instance
     */
    public function updateObserver(Observable $storage) {
        parent::update($this->_measureId, array('state' => $storage->getState()));

        $this->notify();
    }

    /**
     * Returns measure ID.
     *
     * @return int
     */
    public function getMeasureId() {
        return $this->_measureId;
    }

    /**
     * Sets ID of measure. It is for handling in database.
     *
     * @param int $measureId ID of measure
     *
     * @return \PM\Profiler\Monitor\Repository\MySQL
     */
    public function setMeasureId($measureId) {
        $this->_measureId = $measureId;
        $this->_checkMeasureId();

        return $this;
    }

    /**
     * Saves statistics from calls stack in storage. Before it must be saved monitor call fly weight.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     *
     * @return \PM\Profiler\Monitor\Repository\MySQL
     */
    public function saveCallStatistics(Storage $storage) {
        $this->_checkMeasureId();
        $storage->rewind();
        $calls = 0;
        $time  = 0;
        $first = $storage->current();
        $start = $first[Monitor\Enum\CallAttributes::START_TIME];

        $this->_saveStatistics($storage, $calls, $time);
        $this->_updateMeasure($start, $calls, $time);

        return $this;
    }

    /**
     * Loads call statistics into storage.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     *
     * @return \PM\Profiler\Monitor\Interfaces\Storage
     */
    public function loadCallStatistics(Storage $storage) {
        $this->_checkMeasureId();
        // TODO

        return $storage;
    }

    /**
     * Save monitor call fly weight instance. It is required for save others calls.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Call $call Monitor call fly weight instance
     *
     * @return \PM\Profiler\Monitor\Repository\MySQL
     */
    public function saveCallFlyweight(Call $call) {
        $this->_call = $call;

        return $this;
    }

    /**
     * Clean all stored data in database.
     *
     * @return \PM\Profiler\Monitor\Repository\MySQL
     */
    public function reset() {
        $this->_checkMeasureId();
        $this->getDatabase()
            ->delete()
            ->setTable(self::STATS_TABLE)
            ->where('measureId = ?', $this->_measureId)
            ->run();

        $this->getDatabase()
            ->update()
            ->setTable(self::MEASURE_TABLE)
            ->where('id = :id', array(':id' => $this->_measureId))
            ->setUpdateData(array('calls' => 0, 'time' => 0))
            ->run();

        return $this;
    }

    /**
     * It saves all calls to MySQL database.
     *
     * @param array|\PM\Profiler\Monitor\Interfaces\Storage $stack   Monitor storage instance or array with calls
     * @param int                                           $calls   Counter for all calls
     * @param int                                           $time    Counter for time of all calls
     * @param int                                           $parentId ID of parent call
     *
     * @return \PM\Profiler\Monitor\Repository\Cache
     */
    private function _saveStatistics($stack, &$calls, &$time, $parentId = 0) {
        foreach ($stack as $call) {
            $forSave = $call;
            $calls++;
            $time += $call[Monitor\Enum\CallAttributes::TIME];
            if (isset($forSave[Monitor\Enum\CallAttributes::SUB_STACK])) {
                unset($forSave[Monitor\Enum\CallAttributes::SUB_STACK]);
            }

            if (array_key_exists(Monitor\Enum\CallAttributes::START_TIME, $forSave)) {
                unset($forSave[Monitor\Enum\CallAttributes::START_TIME]);
            }

            if (array_key_exists(Monitor\Enum\CallAttributes::END_TIME, $forSave)) {
                unset($forSave[Monitor\Enum\CallAttributes::END_TIME]);
            }

            $forSave[Monitor\Enum\CallAttributes::PARENT]     = $parentId;
            $forSave[Monitor\Enum\CallAttributes::MEASURE_ID] = $this->_measureId;

            if (array_key_exists(Monitor\Enum\CallAttributes::CONTENT, $forSave)) {
                $forSave[Monitor\Enum\CallAttributes::CONTENT] = $this->_call->decodeContentHash($forSave[Monitor\Enum\CallAttributes::CONTENT]);
            }

            if (array_key_exists(Monitor\Enum\CallAttributes::FILE, $forSave)) {
                $forSave[Monitor\Enum\CallAttributes::FILE] = $this->_call->decodeFilenameHash($forSave[Monitor\Enum\CallAttributes::FILE]);
            }

            $id = parent::create($forSave, self::STATS_TABLE);

            $forSave[Monitor\Enum\CallAttributes::ID] = $id;

            unset($forSave);
            if (isset($call[Monitor\Enum\CallAttributes::SUB_STACK])) {
                $this->_saveStatistics($call[Monitor\Enum\CallAttributes::SUB_STACK], $calls, $time, $id);
            }
        }

        return $this;
    }

    /**
     * It updates measure in database with given start time, count of calls and time of whole call stack.
     *
     * @param int $start Start timestamp of measure
     * @param int $calls Count of all calls
     * @param int $time  Time of whole call stack
     *
     * @return \PM\Profiler\Monitor\Repository\MySQL
     */
    private function _updateMeasure($start, $calls, $time) {
        $this->_checkMeasureId();
        $data = array(
            'started' => $this->getUtils()->convertTimeToMySQLDateTime($start / 1000),
            'calls'   => $calls,
            'time'    => $time,
        );

        parent::update($this->_measureId, $data, self::MEASURE_TABLE);

        return $this;
    }

    /**
     * Check is measure ID is valid.
     *
     * @throws Exception Throws when measure ID is not set.
     */
    private function _checkMeasureId() {
        if ($this->_measureId === null) {
            throw new Exception('Measure ID is not set.');
        }
    }
}
