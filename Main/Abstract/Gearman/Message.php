<?php

/**
 * Abstract class for gearman message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Abstract_Gearman_Message {

    /**
     * Data which will be send to worker.
     *
     * @var mixed
     */
    protected $data = null;

    /**
     * Return class name of target gearman worker.
     *
     * @return string
     */
    abstract public function getTarget();

    /**
     * Sets data which will be send to gearman worker.
     *
     * @param mixed $data Data for sending
     *
     * @return Performance_Main_Abstract_Gearman_Message
     */
    final public function setData($data) {
        $this->data = $data;

        return $this;
    }

    /**
     * Returns data.
     *
     * @return mixed
     */
    final public function getData() {
        return $this->data;
    }

    /**
     * Defines what will be serialized.
     *
     * @return array
     */
    final public function __sleep() {
        return array('data');
    }

    /**
     * Returns provider instance
     *
     * @return Performance_Main_Provider
     */
    final protected function getProvider() {
        return Performance_Main_Provider::getInstance();
    }
}
