<?php

namespace PM\Main\Abstracts\Gearman;

use PM\Main\Provider;
use PM\Main\Abstracts\Gearman\Message;

/**
 * Abstract class for gearman worker.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Worker extends \GearmanWorker {

    /**
     * Message instance
     *
     * @var \PM\Main\Abstracts\Gearman\Message
     */
    private $_message = null;

    /**
     * Provider instance
     *
     * @var \PM\Main\Provider
     */
    private $_provider = null;

    /**
     * Construct method.
     *
     * @param \PM\Main\Provider $provider Instance of \PM\Main\Provider
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
        parent::__construct();
    }

    /**
     * Sets message instance.
     *
     * @param \PM\Main\Abstracts\Gearman\Message $message Message instance
     *
     * @return \PM\Main\Abstracts\Gearman\Worker
     */
    final public function setMessage(Message $message) {
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
     * Return message (instance of \PM\Main\Abstracts\Gearman\Message)
     *
     * @return \PM\Main\Abstracts\Gearman\Message
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
     * @return \PM\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
