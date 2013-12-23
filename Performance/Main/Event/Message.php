<?php

/**
 * This script defines class for message mediator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Event_Message implements Performance_Main_Event_Interface_Message {

    /**
     * Data of message.
     *
     * @var mixed
     */
    private $_data = null;

    /**
     * Gets message data.
     *
     * @return mixed
     */
    public function getData() {
        return $this->_data;;
    }

    /**
     * Sets message data.
     *
     * @param mixed $data Data of message
     *
     * @return Performance_Main_Event_Message
     */
    public function setData($data) {
        $this->_data = $data;

        return $this;
    }
}
