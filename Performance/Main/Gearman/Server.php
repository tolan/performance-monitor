<?php

namespace PF\Main\Gearman;

use PF\Main\Provider;
use PF\Main\Abstracts\Gearman\Message;

/**
 * This script defines class for gearman which manage all gearman workers.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Server {

    /**
     * Message with data
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
     * Result for synchronous process
     *
     * @var string
     */
    private $_result = null;

    /**
     * Construct method
     *
     * @param \PF\Main\Provider $provider
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Sets message for worker.
     *
     * @param \PF\Main\Abstracts\Gearman\Message $message Message instance
     *
     * @return \PF\Main\Gearman\Server
     */
    public function setMessage(Message $message) {
        $this->_message = $message;

        return $this;
    }

    /**
     * This manage whole process. It takes target worker from message, then creates worker and sets message and then runs process on worker and sets result.
     *
     * @return \PF\Main\Gearman\Server
     */
    public function run() {
        $target = $this->_message->getTarget();
        $worker = $this->_provider->get($target);

        $worker->setMessage($this->_message);

        $start = microtime(true);

        $worker->process();

        $end = ((microtime(true) - $start) * 1000);
        $this->_provider->get('log')->info('The job taken '.$end.' ms.');

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
