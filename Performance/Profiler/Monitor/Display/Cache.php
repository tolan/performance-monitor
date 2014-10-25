<?php

namespace PM\Profiler\Monitor\Display;

/**
 * This script defines class for display measure in cache.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Cache extends AbstractDisplay {

    /**
     * Method for showing link to measure.
     *
     * @return void
     */
    public function show() {
        // TODO define domain
        echo '<div><a href="Performance/web/#/profiler/file/list">button</a></div>';
    }
}
