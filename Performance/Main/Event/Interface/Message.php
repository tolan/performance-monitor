<?php

/**
 * This script defines interface for message class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Performance_Main_Event_Interface_Message {

    /**
     * Sets message data.
     *
     * @param mixed $data Message data
     */
    public function setData($data);

    /**
     * Gets message data.
     */
    public function getData();
}
