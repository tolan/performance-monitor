<?php

namespace PF\Statistic\Engine;

use PF\Main\Abstracts;
use PF\Main\Interfaces;
use PF\Main\Traits;
use PF\Statistic\Enum\Run;

/**
 * This script defines class for statistic generating run state. This class define transitions between state for run.
 * It is for better manage states and extend posibilities for run entity.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class State extends Abstracts\State implements Interfaces\Observable {

    use Traits\Observable;

    const STATE_IDLE    = Run\State::IDLE;
    const STATE_RUNNING = Run\State::RUNNING;
    const STATE_DONE    = Run\State::DONE;
    const STATE_ERROR   = Run\State::ERROR;

    /**
     * Transition map.
     *
     * @var array
     */
    private $_map = array(
        self::STATE_IDLE => array(
            self::STATE_IDLE,
            self::STATE_RUNNING,
            self::STATE_ERROR
        ),
        self::STATE_RUNNING => array(
            self::STATE_DONE,
            self::STATE_ERROR
        ),
        self::STATE_DONE => array(
            self::STATE_ERROR
        )
    );

    /**
     * Construct method for define transition map.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct(self::STATE_IDLE, $this->_map);
    }

    /**
     * It sets state and notify attached observers.
     *
     * @param mixed $state New state, one of self constants
     *
     * @return State
     */
    public function setState($state) {
        parent::setState($state);

        $this->notify();

        return $this;
    }
}
