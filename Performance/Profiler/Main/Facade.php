<?php

namespace PF\Profiler\Main;

class Facade implements Interfaces\Facade {

    /**
     * Storage instance.
     *
     * @var \PF\Profiler\Main\Interfaces\Storage
     */
    private $_storage;

    /**
     * Ticker instance.
     *
     * @var \PF\Profiler\Main\Interfaces\Ticker
     */
    private $_ticker;

    /**
     * Analyzator instance.
     *
     * @var \PF\Profiler\Main\Interfaces\Analyzator
     */
    private $_analyzator;

    /**
     * Statistic generator instance.
     *
     * @var \PF\Profiler\Main\Interfaces\Statistic
     */
    private $_statistic;

    /**
     * Display instance.
     *
     * @var \PF\Profiler\Main\Interfaces\Display
     */
    private $_display;

    /**
     * Construct method.
     *
     * @param \PF\Profiler\Main\Interfaces\Storage    $storage    Storage instance
     * @param \PF\Profiler\Main\Interfaces\Ticker     $ticker     Ticker instance
     * @param \PF\Profiler\Main\Interfaces\Analyzator $analyzator Analyzator instance
     * @param \PF\Profiler\Main\Interfaces\Statistic  $statistic  Statistic generator instance
     * @param \PF\Profiler\Main\Interfaces\Display    $display    Display instance
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

    public function start() {
        $this->_ticker->start();

        return $this;
    }

    public function stop() {
        $this->_ticker->stop();

        return $this;
    }

    public function isRunning() {
        return $this->_ticker->isRuning();
    }

    public function saveCalls() {
        $this->_storage->saveCalls();

        return $this;
    }

    public function getCalls() {
        return $this->_storage->getCalls();
    }

    public function analyzeCallStack() {
        $this->_analyzator->analyze();

        return $this;
    }

    public function getCallStack() {
        $this->analyzeCallStack();

        return $this->_storage->getCallStack();
    }

    public function generateStatistics() {
        $this->_statistic->generate();

        return $this;
    }

    public function saveStatistics() {
        $this->_storage->saveStatistics();

        return $this;
    }

    public function getStatistics() {
        return $this->_storage->getStatistics();
    }

    public function display() {
        $this->_display->show();

        return $this;
    }
}
