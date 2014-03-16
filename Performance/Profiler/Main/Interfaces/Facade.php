<?php

namespace PF\Profiler\Main\Interfaces;

interface Facade {
    // mediator sender, reciever

    public function __construct(Storage $storage, Ticker $ticker, Analyzator $analyzator, Statistic $statistic, Display $display);

    public function start();
    public function stop();
    public function saveCalls();
    public function getCalls();
    public function analyzeCallStack();
    public function generateStatistics();
    public function saveStatistics();
    public function getStatistics();
    public function display();
}
