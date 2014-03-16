<?php

namespace PF\Profiler\Main\Factory;

use PF\Profiler\Main;

/**
 * This script defines factory class for create facade of performance profiler components.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Facade {

    /**
     * Facade instance.
     *
     * @var \PF\Main\Facade
     */
    private static $_facade = null;

    /**
     * Repository factory instance.
     *
     * @var \PF\Main\Factory\Repository
     */
    private $_repositoryFactory = null;

    /**
     * Display factory instance.
     *
     * @var \PF\Main\Factory\Display
     */
    private $_displayFactory = null;

    /**
     * Construct method.
     *
     * @param \PF\Profiler\Main\Factory\Repository $repositoryFactory Repository factory instance
     * @param \PF\Profiler\Main\Factory\Display    $displayFactory    Display factory instance
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
     * @return \PF\Main\Facade
     */
    public function getFacade() {
        if (self::$_facade === null) {
            $repository = $this->_repositoryFactory->getRepository();
            $call       = new Main\Call();
            $storage    = new Main\Storage($repository, $call, new Main\Storage\State());

            $ticker     = $this->_getTicker($storage, $repository);
            $analyzator = new Main\Analyzator($storage, $call);
            $statistic  = new Main\Statistic($storage, $call);
            $display    = $this->_displayFactory->getDisplay();

            self::$_facade = new Main\Facade($storage, $ticker, $analyzator, $statistic, $display);
        }

        return self::$_facade;
    }

    /**
     * Returns ticker instance.
     *
     * @param \PF\Profiler\Main\Interfaces\Storage    $storage    Storage instance
     * @param \PF\Profiler\Main\Interfaces\Repository $repository Repository instance
     *
     * @return \PF\Profiler\Main\Ticker
     */
    private function _getTicker(Main\Interfaces\Storage $storage, Main\Interfaces\Repository $repository) {
        $ticker  = new Main\Ticker($storage);
        $filters = $repository->loadFilters();

        foreach ($filters as $filter) {
            $ticker->addFilter(new Main\Filter($filter));
        }

        return $ticker;
    }
}
