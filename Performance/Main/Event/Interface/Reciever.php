<?php

/**
 * This script defines interface for reciver class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Performance_Main_Event_Interface_Reciever {

    /**
     * Recieve message from sender.
     *
     * @param Performance_Main_Event_Interface_Message $message Message instance
     * @param Performance_Main_Event_Interface_Sender  $sender  Sender instance
     */
    public function recieve(Performance_Main_Event_Interface_Message $message, Performance_Main_Event_Interface_Sender $sender);
}
