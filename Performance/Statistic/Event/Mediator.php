<?php

namespace PM\Statistic\Event;

/**
 * This script defines class of mediator for statistic module.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Statistic
 */
class Mediator extends \PM\Main\Event\Mediator {

    /**
     * List of recievers which are registered when mediator is created.
     *
     * @var array
     */
    protected $_initRecievers = array(
        'PM\Main\Event\Mediator'
    );
}
