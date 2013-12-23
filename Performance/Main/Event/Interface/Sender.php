<?php

/**
 * This script defines interface for sender class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Performance_Main_Event_Interface_Sender {

    /**
     * Sends message to mediator and then to all recievers.
     *
     * @param Performance_Main_Event_Interface_Message $message Message instance
     */
    public function send(Performance_Main_Event_Interface_Message $message);
}
