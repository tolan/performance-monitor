<?php

namespace PF\Profiler\Monitor\Interfaces;

/**
 * Interface for monitor display.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Display {

    /**
     * Method for showing link, button or anything what is good for inform user about measure.
     *
     * @return void
     */
    public function show();
}
