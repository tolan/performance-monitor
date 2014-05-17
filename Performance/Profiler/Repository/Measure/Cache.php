<?php

namespace PF\Profiler\Repository\Measure;

use PF\Main\Cache as MainCache;
use PF\Main\Filesystem;
use PF\Profiler\Monitor\Repository\Cache as RepositoryCache;
use PF\Profiler\Repository\Interfaces;
use PF\Profiler\Monitor\Enum\CallAttributes;

/**
 * This script defines class for measure repository working with cache.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Cache implements Interfaces\Measure {

    /**
     * Key to cache for monitor call fly weight
     */
    const CACHE_FLYWEIGHT_CALL_KEY = RepositoryCache::CACHE_FLYWEIGHT_CALL_KEY;

    /**
     * Prefix key for each call
     */
    const CACHE_CALL_KEY = RepositoryCache::CACHE_CALL_KEY;

    /**
     * Cache instance.
     *
     * @var \PF\Main\Cache
     */
    private $_cache;

    /**
     * Filesystem directory instance.
     *
     * @var \PF\Main\Filesystem\Directory
     */
    private $_dir = null;

    /**
     * Sets cache instance.
     *
     * @param \PF\Main\Cache $cache Cache instance
     *
     * @return \PF\Profiler\Repository\Measure\Cache
     */
    public function setCache(MainCache $cache) {
        $this->_cache = $cache;

        return $this;
    }

    /**
     * Sets filesystem directory instance for find all measures in filesystem.
     *
     * @param \PF\Main\Filesystem\Directory $dir Filesystem directory instance
     *
     * @return \PF\Profiler\Repository\Measure\Cache
     */
    public function setDir(Filesystem\Directory $dir) {
        $this->_dir = $dir;

        return $this;
    }

    /**
     * Creates new measure in repository with basic data. FAKE
     *
     * @param \PF\Profiler\Entity\Measure $measure Measure entity instance
     */
    public function createMeasure(\PF\Profiler\Entity\Measure $measure) {
        // TODO
    }

    /**
     * Get measure statistics like as count of calls, consumed time, date of start, etc.
     *
     * @param int $measureId ID of measure
     *
     * @return array
     */
    public function getMeasureStatistics($measureId) {
        $result = array(
            'started'      => 0,
            'time'         => 0,
            'calls'        => 0,
            'maxImmersion' => 0
        );

        $result['started'] = $this->_dir->getFile($measureId)->getModificationDate() * 1000;

        $baseCalls = $this->getMeasureCallStack($measureId, 0);
        foreach ($baseCalls as $call) {
            $result['time'] += $call[CallAttributes::TIME_SUB_STACK];
        }

        $calls  = array_diff_key($this->_cache->load(), array(self::CACHE_FLYWEIGHT_CALL_KEY => true));
        $result['calls'] = count($calls);

        foreach ($calls as $call) {
            if (isset($call[CallAttributes::IMMERSION]) && $call[CallAttributes::IMMERSION] > $result['maxImmersion']) {
                $result['maxImmersion'] = $call[CallAttributes::IMMERSION];
            }
        }

        return $result;
    }

    /**
     * Get list statistics information about calls grouped by file and line.
     *
     * @param int $measureId ID of measure
     *
     * @return array
     */
    public function getMeasureCallsStatistic($measureId) {
        $callFlyWeight = $this->_cache->load(self::CACHE_FLYWEIGHT_CALL_KEY); /* @var $callFlyWeight \PF\Profiler\Monitor\Interfaces\Call */

        $calls  = array_diff_key($this->_cache->load(), array(self::CACHE_FLYWEIGHT_CALL_KEY => true));
        $data   = array();
        $result = array();

        foreach ($calls as $call) {
            $content      = isset($call[CallAttributes::CONTENT])        ? $callFlyWeight->decodeContentHash($call[CallAttributes::CONTENT]) : '';
            $file         = isset($call[CallAttributes::FILE])           ? $callFlyWeight->decodeFilenameHash($call[CallAttributes::FILE])   : '';
            $time         = isset($call[CallAttributes::TIME])           ? $call[CallAttributes::TIME] : 0;
            $timeSubStack = isset($call[CallAttributes::TIME_SUB_STACK]) ? $call[CallAttributes::TIME_SUB_STACK] : 0;
            $line         = isset($call[CallAttributes::LINE])           ? $call[CallAttributes::LINE] : 0;
            $start        = isset($call[CallAttributes::START_TIME])     ? $call[CallAttributes::START_TIME] : 0;
            $end          = isset($call[CallAttributes::END_TIME])       ? $call[CallAttributes::END_TIME] : 0;

            $call[CallAttributes::CONTENT]        = $content;
            $call[CallAttributes::FILE]           = $file;
            $call[CallAttributes::TIME]           = $time;
            $call[CallAttributes::TIME_SUB_STACK] = $timeSubStack;
            $call[CallAttributes::START_TIME]     = $start * 1000000;
            $call[CallAttributes::END_TIME]       = $end * 1000000;
            $call['count']                        = 1;
            $call['avgTime']                      = $time;
            $call['timeSubStack']                 = $timeSubStack;
            $call['avgTimeSubStack']              = $timeSubStack;
            $call['min']                          = $time;
            $call['max']                          = $time;

            if (isset($data[$file]) && isset($data[$file][$line])) {
                $item = $data[$file][$line];
                $item['count']++;
                $item['time']            += $time;
                $item['avgTime']          = $item['time'] / $item['count'];
                $item['timeSubStack']    += $timeSubStack;
                $item['avgTimeSubStack']  = $item['timeSubStack'] / $item['count'];
                $item['min']              = min($item['min'], $time);
                $item['max']              = max($item['max'], $time);

                $data[$file][$line] = $item;
            } else {
                if (!isset($data[$file])) {
                    $data[$file] = array();
                }

                $data[$file][$line] = $call;
            }
        }

        foreach ($data as $fileData) {
            foreach ($fileData as $call) {
                $result[] = $call;
            }
        }

        return $result;
    }

    /**
     * Returns list all measures.
     *
     * @return array
     *
     * @throws \PF\Profiler\Exception Throws when dir is not set.
     */
    public function findAll() {
        if ($this->_dir === null) {
            throw new \PF\Profiler\Exception('Dir is not set.');
        }

        $files  = $this->_dir->getAll();
        $result = array();
        foreach ($files as $file) {
            /* @var $file \PF\Main\Filesystem\File */
            $result[] = array(
                'id'     => $file->getName(),
                'edited' => $file->getModificationDate() * 1000,
                'size'   => $file->getFilesize()
            );
        }

        return $result;
    }

    /**
     * Get list of calls by given measure ID and ID of parent call.
     *
     * @param int $measureId ID of measure
     * @param int $parentId  ID of parent call (default 0 it means get root calls)
     *
     * @return array
     */
    public function getMeasureCallStack($measureId, $parentId) {
        $callFlyWeight = $this->_cache->load(self::CACHE_FLYWEIGHT_CALL_KEY); /* @var $callFlyWeight \PF\Profiler\Monitor\Interfaces\Call */

        $calls  = array_diff_key($this->_cache->load(), array(self::CACHE_FLYWEIGHT_CALL_KEY => true));
        $result = array();

        foreach ($calls as $call) {
            if ((string)$call['parentId'] === (string)$parentId) {
                $content      = isset($call[CallAttributes::CONTENT])        ? $callFlyWeight->decodeContentHash($call[CallAttributes::CONTENT]) : '';
                $file         = isset($call[CallAttributes::FILE])           ? $callFlyWeight->decodeFilenameHash($call[CallAttributes::FILE])   : '';
                $time         = isset($call[CallAttributes::TIME])           ? $call[CallAttributes::TIME] : 0;
                $timeSubStack = isset($call[CallAttributes::TIME_SUB_STACK]) ? $call[CallAttributes::TIME_SUB_STACK] : 0;
                $start        = isset($call[CallAttributes::START_TIME])     ? $call[CallAttributes::START_TIME] : 0;
                $end          = isset($call[CallAttributes::END_TIME])       ? $call[CallAttributes::END_TIME] : 0;

                $call[CallAttributes::CONTENT]        = $content;
                $call[CallAttributes::FILE]           = $file;
                $call[CallAttributes::TIME]           = $time;
                $call[CallAttributes::TIME_SUB_STACK] = $timeSubStack;
                $call[CallAttributes::START_TIME]     = $start * 1000000;
                $call[CallAttributes::END_TIME]       = $end * 1000000;

                $result[] = $call;
            }
        }

        return $result;
    }

    /**
     * Deletes measure from repository.
     *
     * @param int $measureId ID of measure
     *
     * @return boolean
     */
    public function deleteMeasure($measureId) {
        $this->_dir->deleteFile($measureId);

        return true;
    }
}
