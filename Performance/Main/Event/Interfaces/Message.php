<?php

namespace PM\Main\Event\Interfaces;

/**
 * This script defines interface for message class.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
interface Message {

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
