<?php

namespace PF\Profiler\Main\Repository;

use PF\Profiler\Main;

abstract class AbstractRepository implements Main\Interfaces\Repository {

    public function loadFilters($setId = null) {
        // TODO implement
        return array();
    }
}
