<?php

namespace PM\Profiler\Monitor\Interfaces;

use PM\Main\Interfaces;
use PM\Profiler\Monitor\Storage\State;

/**
 * Interface for monitor storage.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Storage extends Interfaces\Iterator, Interfaces\ArrayAccess, Interfaces\Observable {

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Repository $repository Monitor repository instance
     * @param \PM\Profiler\Monitor\Interfaces\Call       $call       Call fly weight instance
     * @param \PM\Profiler\Monitor\Storage\State         $state      Monitor storage state instance
     */
    public function __construct(Repository $repository, Call $call, State $state);

    /**
     * Gets call fly weight instance.
     */
    public function getCallInstance();

    /**
     * Gets actual state.
     */
    public function getState();

    /**
     * Sets new state.
     *
     * @param string $state New state of storage
     */
    public function setState($state);

    /**
     * Save statistics into repository system.
     */
    public function saveStatistics();

    /**
     * Gets statistics from repository system.
     */
    public function getStatistics();

    /**
     * Resets all cached and stored data.
     */
    public function reset();
}