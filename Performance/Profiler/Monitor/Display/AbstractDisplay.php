<?php

namespace PF\Profiler\Monitor\Display;

use PF\Profiler\Monitor\Interfaces;

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
