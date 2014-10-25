<?php

namespace PM\Profiler\Monitor\Interfaces;

/**
 * Interface for facade of monitor component.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Facade {

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage    $storage    Monitor storage instance
     * @param \PM\Profiler\Monitor\Interfaces\Ticker     $ticker     Monitor ticker instance
     * @param \PM\Profiler\Monitor\Interfaces\Analyzator $analyzator Monitor analyzator instance
     * @param \PM\Profiler\Monitor\Interfaces\Statistic  $statistic  Monitor statistic instance
     * @param \PM\Profiler\Monitor\Interfaces\Display    $display    Monitor display instance
     */
    public function __construct(Storage $storage, Ticker $ticker, Analyzator $analyzator, Statistic $statistic, Display $display);

    /**
     * Start measure of request.
     */
    public function start();

    /**
     * Stop measure of request.
     */
    public function stop();

    /**
     * Reset all stored data.
     */
    public function reset();

    /**
     * Analyze list of calls to call stack tree.
     */
    public function analyzeCallStack();

    /**
     * Generate statistics for call stack tree.
     */
    public function generateStatistics();

    /**
     * Save statistics into repository system.
     */
    public function saveStatistics();

    /**
     * Gets statistics from repository system.
     */
    public function getStatistics();

    /**
     * This process display function for inform user about measure.
     */
    public function display();

    /**
     * Returns actual state of measure (state is in storage).
     */
    public function getState();
}
