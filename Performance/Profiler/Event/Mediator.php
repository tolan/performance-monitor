<?php

/**
 * This script defines class of mediator for profiler module.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Event_Mediator extends Performance_Main_Event_Mediator {

    /**
     * List of recievers which are registered when mediator is created.
     *
     * @var array
     */
    protected $_initRecievers = array(
        'Performance_Main_Event_Mediator'
    );
}
