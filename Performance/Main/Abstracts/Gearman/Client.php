<?php

namespace PM\Main\Abstracts\Gearman;

use PM\Main\Provider;
use PM\Main\Gearman\Enum\ServerFunction;

/**
 * Abstract class for gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Client {

    /**
     * Gearman Client instance
     *
     * @var \GearmanClient
     */
    private $_client = null;

    /**
     * Performance provider instance
     *
     * @var \PM\Main\Provider
     */
    private $_provider = null;

    /**
     * Construct method.
     *
     * @param \GearmanClient    $client   Instance of \GearmanClient
     * @param \PM\Main\Provider $provider Instance of \PM\Main\Provider
     */
    final public function __construct(\GearmanClient $client, Provider $provider) {
        $client->addServer();
        $this->_client   = $client;
        $this->_provider = $provider;
        $this->init();
    }

    /**
     * Optional constructor function for each gearman client.
     *
     * @return void
     */
    protected function init() {}

    /**
     * Return instance of geraman message.
     *
     * @return \PM\Main\Abstracts\Gearman\Message
     */
    abstract protected function getMessage();

    /**
     * Return provider instance
     *
     * @return \PM\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Return real gearman client.
     *
     * @return \GearmanClient
     */
    final protected function getClient() {
        return $this->_client;
    }

    /**
     * Sets data in massage which is send to worker.
     *
     * @param mixed $data Some data
     *
     * @return \PM\Main\Abstracts\Gearman\Client
     */
    final public function setData($data) {
        $this->getMessage()->setData($data);

        return $this;
    }

    /**
     * Run synchronous process in gearman.
     *
     * @return \PM\Main\Abstracts\Gearman\Client
     */
    final public function doSynchronize() {
        $data = $this->_getWorkerData();

        $this->getClient()->do(ServerFunction::GEARMAN_FUNCTION, $data);

        return $this;
    }

    /**
     * Run parallel asynchronous process in gearman.
     *
     * @return \PM\Main\Abstracts\Gearman\Client
     */
    final public function doAsynchronize() {
        $data = $this->_getWorkerData();

        $this->getClient()->doBackground(ServerFunction::GEARMAN_FUNCTION, $data);

        return $this;
    }

    /**
     * Gets data for worker from message.
     *
     * @return string
     */
    private function _getWorkerData() {
        return serialize($this->getMessage());
    }
}
