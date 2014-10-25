<?php

namespace PM\Main\Event\Interfaces;

/**
 * This script defines interface for reciver class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Reciever {

    /**
     * Recieve message from sender.
     *
     * @param \PM\Main\Event\Interfaces\Message $message Message instance
     * @param \PM\Main\Event\Interfaces\Sender  $sender  Sender instance
     */
    public function recieve(Message $message, Sender $sender);
}
