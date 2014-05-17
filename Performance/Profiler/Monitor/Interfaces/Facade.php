<?php

namespace PF\Profiler\Monitor\Interfaces;

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
     * @param \PF\Profiler\Monitor\Interfaces\Storage    $storage    Monitor storage instance
     * @param \PF\Profiler\Monitor\Interfaces\Ticker     $ticker     Monitor ticker instance
     * @param \PF\Profiler\Monitor\Interfaces\Analyzator $analyzator Monitor analyzator instance
     * @param \PF\Profiler\Monitor\Interfaces\Statistic  $statistic  Monitor statistic instance
     * @param \PF\Profiler\Monitor\Interfaces\Display    $display    Monitor display instance
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
