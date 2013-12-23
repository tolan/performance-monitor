<?php

/**
 * This script defines interface for mediator class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Performance_Main_Event_Interface_Mediator {

    /**
     * Register reciever for send message. Message is sent to the recipient by message type that receives its parameter (include children in object model).
     *
     * @param Performance_Main_Event_Interface_Reciever $reciever Reciver
     */
    public function register(Performance_Main_Event_Interface_Reciever $reciever);

    /**
     * This method provides the deregistration of the mediator.
     *
     * @param Performance_Main_Event_Interface_Reciever $reciever Reciver
     */
    public function unregister(Performance_Main_Event_Interface_Reciever $reciever);

    /**
     * This method sends message to all recievers which has concrete message class in its parameter.
     *
     * @param Performance_Main_Event_Interface_Message $message Message instance
     * @param Performance_Main_Event_Interface_Sender  $sender  Sender instance
     */
    public function send(Performance_Main_Event_Interface_Message $message, Performance_Main_Event_Interface_Sender $sender);
}
