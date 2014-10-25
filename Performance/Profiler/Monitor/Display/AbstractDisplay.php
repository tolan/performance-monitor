<?php

namespace PM\Profiler\Monitor\Display;

use PM\Profiler\Monitor\Interfaces;

/**
 * This script defines abstract class for monitor display.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractDisplay implements Interfaces\Display {

    /**
     * Method for showing link, button or anything what is good for inform user about measure.
     *
     * @return void
     */
    public function show() {}
}
