<?php

namespace PF\Profiler\Monitor\Interfaces;

/**
 * Interface for monitor statistic.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Statistic {

    /**
     * Construct method.
     *
     * @param \PF\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     */
    public function __construct(Storage $storage);

    /**
     * Generate statistics information in call stack tree.
     */
    public function generate();
}
