<?php

namespace PM\Profiler\Monitor;

/**
 * This script defines class for monitor facade. It provides simple API for manipulation of monitor component.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Facade implements Interfaces\Facade {

    /**
     * Storage instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Storage
     */
    private $_storage;

    /**
     * Ticker instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Ticker
     */
    private $_ticker;

    /**
     * Analyzator instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Analyzator
     */
    private $_analyzator;

    /**
     * Statistic generator instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Statistic
     */
    private $_statistic;

    /**
     * Display instance.
     *
     * @var \PM\Profiler\Monitor\Interfaces\Display
     */
    private $_display;

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage    $storage    Storage instance
     * @param \PM\Profiler\Monitor\Interfaces\Ticker     $ticker     Ticker instance
     * @param \PM\Profiler\Monitor\Interfaces\Analyzator $analyzator Analyzator instance
     * @param \PM\Profiler\Monitor\Interfaces\Statistic  $statistic  Statistic generator instance
     * @param \PM\Profiler\Monitor\Interfaces\Display    $display    Display instance
     *
     * @return void
     */
    public function __construct(
        Interfaces\Storage $storage,
        Interfaces\Ticker $ticker,
        Interfaces\Analyzator $analyzator,
        Interfaces\Statistic $statistic,
        Interfaces\Display $display
    ) {
        $this->_storage    = $storage;
        $this->_ticker     = $ticker;
        $this->_analyzator = $analyzator;
        $this->_statistic  = $statistic;
        $this->_display    = $display;
    }

    /**
     * Start measure of request.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function start() {
        $this->_ticker->start();

        return $this;
    }

    /**
     * Stop measure of request.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function stop() {
        $this->_ticker->stop();

        return $this;
    }

    /**
     * Reset all stored data.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function reset() {
        $this->_storage->reset();

        return $this;
    }

    /**
     * Returns wheter measure is running.
     *
     * @return boolean
     */
    public function isRunning() {
        return $this->_ticker->isRuning();
    }

    /**
     * Returns actual state of measure.
     *
     * @return enum
     */
    public function getState() {
        return $this->_storage->getState();
    }

    /**
     * Returns actual calls in storage.
     *
     * @return array
     */
    public function getCalls() {
        return $this->_storage->toArray();
    }

    /**
     * Analyze list of calls to call stack tree.
     * Attention: storage must be in right state.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function analyzeCallStack() {
        $this->_analyzator->analyze();

        return $this;
    }

    /**
     * Generate statistics for call stack tree.
     * Attention: storage must be in right state.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function generateStatistics() {
        $this->_statistic->generate();

        return $this;
    }

    /**
     * Save statistics into repository system.
     * Attention: storage must be in right state.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function saveStatistics() {
        $this->_storage->saveStatistics();

        return $this;
    }

    /**
     * Gets statistics from repository system.
     *
     * @return array
     */
    public function getStatistics() {
        return $this->_storage->getStatistics();
    }

    /**
     * This process display function for inform user about measure.
     *
     * @return \PM\Profiler\Monitor\Facade
     */
    public function display() {
        $this->_display->show();

        return $this;
    }
}
