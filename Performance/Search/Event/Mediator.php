<?php

namespace PM\Search\Event;

/**
 * This script defines class of mediator for search module.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Search
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
