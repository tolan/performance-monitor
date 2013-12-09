<?php

/**
 * Abstract class for gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Abstract_Gearman_Worker extends GearmanWorker {

    /**
     * Message instance
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
     * Construct method.
     *
     * @param Performance_Main_Provider $provider Instance of Performance_Main_Provider
     *
     * @return Performance_Main_Abstract_Gearman_Worker
     */
    public function __construct(Performance_Main_Provider $provider) {
        $this->_provider = $provider;
        parent::__construct();
    }

    /**
     * Sets message instance.
     *
     * @param Performance_Main_Abstract_Gearman_Message $message Message instance
     *
     * @return Performance_Main_Abstract_Gearman_Worker
     */
    final public function setMessage(Performance_Main_Abstract_Gearman_Message $message) {
        $this->_message = $message;

        return $this;
    }

    /**
     * Process method.
     *
     * @return void
     */
    abstract public function process();

    /**
     * Return result for synchronous process.
     *
     * @return string
     */
    abstract public function getResult();

    /**
     * Return message (instance of Performance_Main_Abstract_Gearman_Message)
     *
     * @return Performance_Main_Abstract_Gearman_Message
     */
    final protected function getMessage() {
        return $this->_message;
    }

    /**
     * Return data from message.
     *
     * @return mixed
     */
    final protected function getMessageData() {
        return $this->_message->getData();
    }

    /**
     * Returns instance of provider.
     *
     * @return Performance_Main_Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
