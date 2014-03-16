<?php

namespace PF\Profiler\Main\Interfaces;

interface Repository {

    public function loadFilters($setId = null);
    public function saveCalls(Storage $storage);
    public function loadCalls(Storage $storage);
    public function saveCallStatistics(Storage $storage);
    public function loadCallStatistics(Storage $storage);
    public function saveCallFlyweight(Call $call);
    public function loadCallFlyweight();
}
