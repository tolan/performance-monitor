<?php

namespace PM\Profiler\Monitor\Interfaces;

use PM\Profiler\Entity;

/**
 * Interface for monitor request filter.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Filter {

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Entity\Filter $filter Filter entity instance
     */
    public function __construct(Entity\Filter $filter);

    /**
     * Returns whether backtrace is allowed by filter rules.
     *
     * @param array $backtrace Backtrace
     */
    public function isAllowedBacktrace($backtrace);
}
