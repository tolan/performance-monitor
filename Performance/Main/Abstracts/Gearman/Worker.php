<?php

namespace PF\Main\Abstracts\Gearman;

use PF\Main\Provider;
use PF\Main\Abstracts\Gearman\Message;

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
     * @var \PF\Main\Abstracts\Gearman\Message
     */
    private $_message = null;

    /**
     * Provider instance
     *
     * @var \PF\Main\Provider
     */
    private $_provider = null;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Instance of \PF\Main\Provider
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
        parent::__construct();
    }

    /**
     * Sets message instance.
     *
     * @param \PF\Main\Abstracts\Gearman\Message $message Message instance
     *
     * @return \PF\Main\Abstracts\Gearman\Worker
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
     * Return message (instance of \PF\Main\Abstracts\Gearman\Message)
     *
     * @return \PF\Main\Abstracts\Gearman\Message
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
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
