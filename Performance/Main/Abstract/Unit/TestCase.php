<?php

/**
 * Abstract class for php unit test case.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class Performance_Main_Abstract_Unit_TestCase extends PHPUnit_Framework_TestCase {

    /**
     * Provider instance.
     *
     * @var Performance_Main_Provider
     */
    private $_provider;

    /**
     * Construct method. Override and call parent method.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        $this->_provider = Performance_Main_Provider::getInstance();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Returns provider instance.
     *
     * @return Performance_Main_Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }
}
