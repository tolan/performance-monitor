<?php

namespace PF\Profiler\Monitor\Repository;

use PF\Main\Cache as MainCache;
use PF\Main\Interfaces\Observable;
use PF\Profiler\Monitor;
use PF\Profiler\Monitor\Enum\CallAttributes;

/**
 * This script defines class for monitor repository working with cache.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Cache extends AbstractRepository {

    /**
     * Cache instance
     *
     * @var \PF\Main\Cache
     */
    private $_cache;

    /**
     * Key to call
     */
    const CACHE_CALL_KEY = 'profiler_call_';

    /**
     * Key to monitor call fly weight
     */
    const CACHE_FLYWEIGHT_CALL_KEY = 'profiler_call';

    /**
     * Sets cache instance.
     *
     * @param \PF\Main\Cache $cache
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    public function setCache(MainCache $cache) {
        $this->_cache = $cache;

        return $this;
    }

    /**
     * Method for notify that observable is updated.
     *
     * @param \PF\Main\Interfaces\Observable $subject Observable instance
     *
     * @return void
     */
    public function updateObserver(Observable $subject) {}

    /**
     * Saves statistics from calls stack in storage. Before it must be saved monitor call fly weight.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    public function saveCallStatistics(Monitor\Interfaces\Storage $storage) {
        $this->_saveStats($storage);
        $this->_cache->commit();

        return $this;
    }

    /**
     * Loads call statistics into storage.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     *
     * @return \PF\Profiler\Monitor\Interfaces\Storage
     */
    public function loadCallStatistics(Monitor\Interfaces\Storage $storage) {
        $callFlyWeight = $this->_cache->load(self::CACHE_FLYWEIGHT_CALL_KEY); /* @var $callFlyWeight \PF\Profiler\Monitor\Interfaces\Call */

        $storage->setState(Monitor\Storage\State::STATE_STAT_GENERATED);
        $storage->fromArray(array_diff_key($this->_cache->load(), array(self::CACHE_FLYWEIGHT_CALL_KEY => true)));

        foreach ($storage as $key => $call) {
            $call[CallAttributes::CONTENT] = $callFlyWeight->decodeContentHash($call[CallAttributes::CONTENT]);
            $call[CallAttributes::FILE]    = $callFlyWeight->decodeFilenameHash($call[CallAttributes::FILE]);

            $storage[$key] = $call;
        }

        return $storage;
    }

    /**
     * Save monitor call fly weight instance. It is required for save others calls.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Call $call Monitor call fly weight instance
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    public function saveCallFlyweight(Monitor\Interfaces\Call $call) {
        $this->_cache->save(self::CACHE_FLYWEIGHT_CALL_KEY, $call);
        $this->_cache->commit();

        return $this;
    }

    /**
     * Clean cache and all data.
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    public function reset() {
        $this->_cache->clean();

        return $this;
    }

    /**
     * It saves all calls to cache.
     *
     * @param array|\PF\Profiler\Monitor\Interfaces\Storage $stack    Monitor storage instance or array with calls
     * @param int                                           $parentId ID of parent call
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    private function _saveStats($stack, $parentId = 0) {
        foreach ($stack as $call) {
            $forSave = $call;
            if (isset($forSave[CallAttributes::SUB_STACK])) {
                unset($forSave[CallAttributes::SUB_STACK]);
            }

            $id = self::CACHE_CALL_KEY.uniqid();

            $forSave[CallAttributes::ID]     = $id;
            $forSave[CallAttributes::PARENT] = $parentId;

            $this->_cache->save($id, $forSave);

            unset($forSave);
            if (isset($call[CallAttributes::SUB_STACK])) {
                $this->_saveStats($call[CallAttributes::SUB_STACK], $id);
            }
        }

        return $this;
    }
}
