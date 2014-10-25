<?php

namespace PM\Profiler\Monitor\Interfaces;

/**
 * Interface for monitor ticker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Ticker {

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     */
    public function __construct (Storage $storage);

    /**
     * Adds filter to filtering rules.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Filter $filter Monitor filter instance
     */
    public function addFilter (Filter $filter);

    /**
     * Tick method. This is called every time when a call is detected.
     */
    public function tick();

    /**
     * Start ticking.
     */
    public function start();

    /**
     * Stop ticking.
     */
    public function stop();

    /**
     * Returns wheter ticking is enabled.
     */
    public function isRuning();
}
