<?php

namespace PM\Profiler\Monitor\Interfaces;

/**
 * Interface for monitor analyzator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Analyzator {

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     */
    public function __construct(Storage $storage);

    /**
     * Analyze list of calls in storage into call stack tree.
     * It replace data in storage.
     */
    public function analyze();
}
