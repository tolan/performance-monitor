<?php

/**
 * Abstract class for gearman client.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Abstract_Gearman_Client {

    /**
     * Gearman Client instance
     *
     * @var GearmanClient
     */
    private $_client = null;

    /**
     * Performance provider instance
     *
     * @var Performance_Main_Provider
     */
    private $_provider = null;

    /**
     * Construct method.
     *
     * @param GearmanClient             $client   Instance of GearmanClient
     * @param Performance_Main_Provider $provider Instance of Performance_Main_Provider
     *
     * @return Performance_Main_Abstract_Gearman_Client
     */
    final public function __construct(GearmanClient $client, Performance_Main_Provider $provider) {
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
     * @return Performance_Main_Abstract_Gearman_Message
     */
    abstract protected function getMessage();

    /**
     * Return provider instance
     *
     * @return Performance_Main_Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }

    /**
     * Return real gearman client.
     *
     * @return GearmanClient
     */
    final protected function getClient() {
        return $this->_client;
    }

    /**
     * Sets data in massage which is send to worker.
     *
     * @param mixed $data Some data
     *
     * @return Performance_Main_Abstract_Gearman_Client
     */
    final public function setData($data) {
        $this->getMessage()->setData($data);

        return $this;
    }

    /**
     * Run synchronous process in gearman.
     *
     * @return Performance_Main_Abstract_Gearman_Client
     */
    final public function doSynchronize() {
        $data = $this->_getWorkerData();

        $this->getClient()->do(Performance_Main_Gearman_Enum_ServerFunction::GEARMAN_FUNCTION, $data);

        return $this;
    }

    /**
     * Run parallel asynchronous process in gearman.
     *
     * @return Performance_Main_Abstract_Gearman_Client
     */
    final public function doAsynchronize() {
        $data = $this->_getWorkerData();

        $this->getClient()->doBackground(Performance_Main_Gearman_Enum_ServerFunction::GEARMAN_FUNCTION, $data);

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
