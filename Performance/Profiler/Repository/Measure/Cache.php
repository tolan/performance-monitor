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

        $calls           = $this->_cache->load();
        $result['calls'] = count($calls) - 1;

        foreach ($calls as $key => $call) {
            if ($key !== self::CACHE_FLYWEIGHT_CALL_KEY) {
                if(isset($call[CallAttributes::IMMERSION]) && $call[CallAttributes::IMMERSION] > $result['maxImmersion']) {
                    $result['maxImmersion'] = $call[CallAttributes::IMMERSION];
                }

                if ((string)$call['parentId'] === '0' && isset($call[CallAttributes::TIME])) {
                    $result['time'] += $call[CallAttributes::TIME];
                }
                if ((string)$call['parentId'] === '0' && isset($call[CallAttributes::TIME_SUB_STACK])) {
                    $result['time'] += $call[CallAttributes::TIME_SUB_STACK];
                }
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
        $calls         = $this->_cache->load();
        $callFlyWeight = $calls[self::CACHE_FLYWEIGHT_CALL_KEY];
        $data          = array();

        foreach ($calls as $key => $call) {
            if ($key !== self::CACHE_FLYWEIGHT_CALL_KEY) {
                $file                          = isset($call[CallAttributes::FILE]) ? $callFlyWeight->decodeFilenameHash($call[CallAttributes::FILE])   : '';
                $line                          = isset($call[CallAttributes::LINE]) ? $call[CallAttributes::LINE] : 0;
                $time                          = isset($call[CallAttributes::TIME]) ? $call[CallAttributes::TIME] : 0;
                $timeSubStack                  = $time + (isset($call[CallAttributes::TIME_SUB_STACK]) ? $call[CallAttributes::TIME_SUB_STACK] : 0);

                if (!isset($data[$file])) {
                    $data[$file] = array();
                }

                if (isset($data[$file][$line])) {
                    $item = &$data[$file][$line];

                    $item['count']++;
                    $item['time']         += $time;
                    $item['timeSubStack'] += $timeSubStack;
                    if ($item['minTime'] > $time) {
                        $item['minTime'] = $time;
                    }

                    if ($item['maxTime'] < $time) {
                        $item['maxTime'] =  $time;
                    }

                    if ($item['minTimeSubStack'] > $timeSubStack) {
                        $item['minTimeSubStack']  =  $timeSubStack;
                    }

                    if ($item['maxTimeSubStack'] < $timeSubStack) {
                        $item['maxTimeSubStack']  = $timeSubStack;
                    }
                } else {
                    $call[CallAttributes::CONTENT]        = isset($call[CallAttributes::CONTENT])
                        ? $callFlyWeight->decodeContentHash($call[CallAttributes::CONTENT]) : '';
                    $call[CallAttributes::FILE]           = $file;
                    $call[CallAttributes::TIME]           = $time;
                    $call[CallAttributes::TIME_SUB_STACK] = $timeSubStack;
                    $call['minTime']                      = $time;
                    $call['maxTime']                      = $time;
                    $call['minTimeSubStack']              = $timeSubStack;
                    $call['maxTimeSubStack']              = $timeSubStack;
                    $call['count']                        = 1;

                    $data[$file][$line] = $call;
                }
            }
        }

        $result = array();
        foreach ($data as $fileData) {
            foreach ($fileData as $call) {
                $call['avgTime']         = $call['time'] / $call['count'];
                $call['avgTimeSubStack'] = $call['timeSubStack'] / $call['count'];
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
    public function getMeasureCallStack($measureId, $parentId = 0) {
        $calls         = $this->_cache->load();
        $callFlyWeight = $calls[self::CACHE_FLYWEIGHT_CALL_KEY]; /* @var $callFlyWeight \PF\Profiler\Monitor\Interfaces\Call */
        $result        = array();
        $parentId      = (string)$parentId;

        foreach ($calls as $key => $call) {
            if ($key !== self::CACHE_FLYWEIGHT_CALL_KEY && (string)$call['parentId'] === $parentId) {
                $content      = isset($call[CallAttributes::CONTENT])        ? $callFlyWeight->decodeContentHash($call[CallAttributes::CONTENT]) : '';
                $file         = isset($call[CallAttributes::FILE])           ? $callFlyWeight->decodeFilenameHash($call[CallAttributes::FILE])   : '';
                if (!isset($call[CallAttributes::TIME])) {
                    $call[CallAttributes::TIME] = 0;
                }

                if (!isset($call[CallAttributes::TIME_SUB_STACK])) {
                    $call[CallAttributes::TIME_SUB_STACK] =  0;
                }

                $call[CallAttributes::CONTENT]         = $content;
                $call[CallAttributes::FILE]            = $file;
                $call[CallAttributes::TIME_SUB_STACK] += $call[CallAttributes::TIME];

                if (isset($call[CallAttributes::SUB_STACK])) {
                    unset($call[CallAttributes::SUB_STACK]);
                }

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
