<?php

namespace PF\Main\Abstracts;

use PF\Main\Commander;

/**
 * Abstract class for service.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Service {

    /**
     * Commander instance.
     *
     * @var \PF\Main\Commander
     */
    private $_commander;

    /**
     * Construct method.
     *
     * @param \PF\Main\Commander $commander Commander instance
     */
    final public function __construct(Commander $commander) {
        $this->_commander = $commander;

        $this->init();
    }

    /**
     * Returns instance of executor by given name.
     *
     * @param string $name Identificator name for executor [optional]
     *
     * @return \PF\Main\Commander\Executor
     */
    final function getExecutor($name = null) {
        if ($name === null) {
            $name = uniqid();
        }

        return $this->_commander->getExecutor('service_'.$name);
    }

    /**
     * Optional init function for prepare attributes
     *
     * @return \PF\Main\Abstracts\Service
     */
    protected function init() {}
}
