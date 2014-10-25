<?php

namespace PM\Main\Event\Interfaces;

/**
 * This script defines interface for sender class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Sender {

    /**
     * Sends message to mediator and then to all recievers.
     *
     * @param mixed $message Message instance
     */
    public function send($message);
}
