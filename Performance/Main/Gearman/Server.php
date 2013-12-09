<?php

/**
 * This script defines class for gearman which manage all gearman workers.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Gearman_Server {

    /**
     * Message with data
     *
     * @var Performance_Main_Abstract_Gearman_Message
     */
    private $_message = null;

    /**
     * Provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider = null;

    /**
     * Result for synchronous process
     *
     * @var string
     */
    private $_result = null;

    /**
     * Construct method
     *
     * @param Performance_Main_Provider $provider
     */
    public function __construct(Performance_Main_Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Sets message for worker.
     *
     * @param Performance_Main_Abstract_Gearman_Message $message Message instance
     *
     * @return Performance_Main_Gearman_Server
     */
    public function setMessage(Performance_Main_Abstract_Gearman_Message $message) {
        $this->_message = $message;

        return $this;
    }

    /**
     * This manage whole process. It takes target worker from message, then creates worker and sets message and then runs process on worker and sets result.
     *
     * @return Performance_Main_Gearman_Server
     */
    public function run() {
        $target = $this->_message->getTarget();
        $worker = $this->_provider->get($target);

        $worker->setMessage($this->_message);
        $worker->process();
        $this->_result = $worker->getResult();

        return $this;
    }

    /**
     * Returns resut from worker.
     *
     * @return string
     */
    public function getResult() {
        return $this->_result;
    }
}
