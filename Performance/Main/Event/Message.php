<?php

namespace PM\Main\Event;

/**
 * This script defines class for message mediator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Message implements Interfaces\Message {

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
        return $this->_data;
    }

    /**
     * Sets message data.
     *
     * @param mixed $data Data of message
     *
     * @return \PM\Main\Event\Message
     */
    public function setData($data) {
        $this->_data = $data;

        return $this;
    }
}
