<?php

namespace PM\Profiler\Monitor\Storage;

use PM\Main\Abstracts;

/**
 * This script defines class for monitor storage state. This class define transitions between state for monitor storage.
 * It is for better manage states and extend posibilities for monitor storage class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class State extends Abstracts\State {

    const STATE_EMPTY           = 'empty';
    const STATE_TICKING         = 'ticking';
    const STATE_TICKED          = 'ticked';
    const STATE_ANALYZING       = 'analyzing';
    const STATE_ANALYZED        = 'analyzed';
    const STATE_STAT_GENERATING = 'statistic_generating';
    const STATE_STAT_GENERATED  = 'statistic_generated';
    const STATE_ERROR           = 'error';

    /**
     * Transition map.
     *
     * @var array
     */
    private $_map = array(
        self::STATE_EMPTY => array(
            self::STATE_TICKING,
            self::STATE_TICKED,
            self::STATE_EMPTY,
            self::STATE_ERROR
        ),
        self::STATE_TICKING => array(
            self::STATE_TICKED,
            self::STATE_TICKING,
            self::STATE_EMPTY,
            self::STATE_ERROR
        ),
        self::STATE_TICKED => array(
            self::STATE_TICKING,
            self::STATE_ANALYZING,
            self::STATE_ANALYZED,
            self::STATE_EMPTY,
            self::STATE_ERROR
        ),
        self::STATE_ANALYZING => array(
            self::STATE_ANALYZED,
            self::STATE_EMPTY,
            self::STATE_ERROR
        ),
        self::STATE_ANALYZED => array(
            self::STATE_STAT_GENERATING,
            self::STATE_STAT_GENERATED,
            self::STATE_EMPTY,
            self::STATE_ERROR
        ),
        self::STATE_STAT_GENERATING => array(
            self::STATE_STAT_GENERATED,
            self::STATE_EMPTY,
            self::STATE_ERROR
        ),
        self::STATE_STAT_GENERATED  => array(
            self::STATE_EMPTY,
            self::STATE_ERROR
        )
    );

    /**
     * Construct method for define transition map.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(self::STATE_EMPTY, $this->_map);
    }
}
