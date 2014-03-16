<?php

namespace PF\Profiler\Main\Repository;

use PF\Profiler\Main;

class Cache extends AbstractRepository {

    /**
     *
     * @var \PF\Main\Cache
     */
    private $_cache;

    const CACHE_KEY = 'profiler_';

    public function setCache(\PF\Main\Cache $cache) {
        $this->_cache = $cache;
    }

    public function saveCalls(Main\Interfaces\Storage $storage) {
        $this->_cache->save(self::CACHE_KEY.$storage->getState(), $storage->getCalls());

        return $this;
    }

    public function loadCalls(Main\Interfaces\Storage $storage) {
        $storage->setState(Main\Storage\State::STATE_TICKED);
        $storage->fromArray($this->_cache->load(self::CACHE_KEY.$storage->getState()));

        return $storage;
    }

    public function saveCallStatistics(Main\Interfaces\Storage $storage) {
        $this->_cache->save(self::CACHE_KEY.$storage->getState(), $storage->getStatistics());

        return $this;
    }

    public function loadCallStatistics(Main\Interfaces\Storage $storage) {
        $storage->setState(Main\Storage\State::STATE_STAT_GENERATED);
        $storage->fromArray($this->_cache->load(self::CACHE_KEY.$storage->getState()));

        return $storage;
    }

    public function saveCallFlyweight(Main\Interfaces\Call $call) {
        $this->_cache->save(self::CACHE_KEY.'call', $call);

        return $this;
    }

    public function loadCallFlyweight() {
        return $this->_cache->load(self::CACHE_KEY.'call');
    }
}
