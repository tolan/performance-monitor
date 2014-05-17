<?php

namespace PF\Profiler\Monitor\Factory;

use PF\Profiler\Monitor;

/**
 * This script defines factory class for create facade for performance profiler components.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Facade {

    /**
     * Facade instance.
     *
     * @var \PF\Monitor\Facade
     */
    private static $_facade = null;

    /**
     * Repository factory instance.
     *
     * @var \PF\Monitor\Factory\Repository
     */
    private $_repositoryFactory = null;

    /**
     * Display factory instance.
     *
     * @var \PF\Monitor\Factory\Display
     */
    private $_displayFactory = null;

    /**
     * Construct method.
     *
     * @param \PF\Profiler\Monitor\Factory\Repository $repositoryFactory Repository factory instance
     * @param \PF\Profiler\Monitor\Factory\Display    $displayFactory    Display factory instance
     *
     * @return void
     */
    public function __construct(Repository $repositoryFactory, Display $displayFactory) {
        $this->_repositoryFactory = $repositoryFactory;
        $this->_displayFactory    = $displayFactory;
    }

    /**
     * Returns instance of facade for performance profiler components
     *
     * @return \PF\Monitor\Facade
     */
    public function getFacade() {
        if (self::$_facade === null) {
            $repository = $this->_repositoryFactory->getRepository();
            $call       = new Monitor\Call();
            $storage    = new Monitor\Storage($repository, $call, new Monitor\Storage\State());
            $storage->attach($repository);

            $ticker     = $this->_getTicker($storage, $repository);
            $analyzator = new Monitor\Analyzator($storage, $call);
            $statistic  = new Monitor\Statistic($storage, $call);
            $display    = $this->_displayFactory->getDisplay();

            self::$_facade = new Monitor\Facade($storage, $ticker, $analyzator, $statistic, $display);
        }

        return self::$_facade;
    }

    /**
     * Returns ticker instance.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Storage    $storage    Storage instance
     * @param \PF\Profiler\Monitor\Interfaces\Repository $repository Repository instance
     *
     * @return \PF\Profiler\Monitor\Ticker
     */
    private function _getTicker(Monitor\Interfaces\Storage $storage, Monitor\Interfaces\Repository $repository) {
        $ticker  = new Monitor\Ticker($storage);
        $filters = $repository->loadFilters();

        foreach ($filters as $filter) {
            $ticker->addFilter(new Monitor\Filter($filter));
        }

        return $ticker;
    }
}
