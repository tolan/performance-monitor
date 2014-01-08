<?php

namespace PF\Main\Abstracts\Unit;

use PF\Main\Provider;

/**
 * Abstract class for php unit test case.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * Provider instance.
     *
     * @var \PF\Main\Provider
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
        $this->_provider = Provider::getInstance();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Returns provider instance.
     *
     * @return \PF\Main\Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }
}
