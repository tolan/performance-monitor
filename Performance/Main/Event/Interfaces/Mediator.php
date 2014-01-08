<?php

namespace PF\Main\Event\Interfaces;

/**
 * This script defines interface for mediator class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Mediator {

    /**
     * Register reciever for send message. Message is sent to the recipient by message type that receives its parameter (include children in object model).
     *
     * @param \PF\Main\Event\Interfaces\Reciever $reciever Reciver
     */
    public function register(Reciever $reciever);

    /**
     * This method provides the deregistration of the mediator.
     *
     * @param \PF\Main\Event\Interfaces\Reciever $reciever Reciver
     */
    public function unregister(Reciever $reciever);

    /**
     * This method sends message to all recievers which has concrete message class in its parameter.
     *
     * @param \PF\Main\Event\Interfaces\Message $message Message instance
     * @param \PF\Main\Event\Interfaces\Sender  $sender  Sender instance
     */
    public function send(Message $message, Sender $sender);
}
