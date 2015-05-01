<?php

namespace PM\Cron\Event;

/**
 * This script defines class of mediator for cron module.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Cron
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
