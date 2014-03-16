<?php

namespace PF\Profiler\Main\Repository;

class MySQL extends AbstractRepository {

    public function saveCalls(Main\Interfaces\Storage $storage) {

        return $this;
    }

    public function loadCalls() {
        // TODO create it in factory
        $storage = new Main\Storage($this);

        return $storage;
    }

    public function saveCallStatistics(Main\Interfaces\Storage $storage) {

        return $this;
    }

    public function loadCallStatistics() {
        // TODO create it in factory
        $storage = new Main\Storage($this);

        return $storage;
    }
}
