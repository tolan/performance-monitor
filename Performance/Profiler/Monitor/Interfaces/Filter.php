<?php

namespace PF\Profiler\Monitor\Interfaces;

use PF\Profiler\Entity;

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
     * @param \PF\Profiler\Entity\Filter $filter Filter entity instance
     */
    public function __construct(Entity\Filter $filter);

    /**
     * Returns whether backtrace is allowed by filter rules.
     *
     * @param array $backtrace Backtrace
     */
    public function isAllowedBacktrace($backtrace);
}
