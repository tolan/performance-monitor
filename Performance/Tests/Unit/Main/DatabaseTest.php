<?php

namespace PM\Tests\Unit\Main;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Database.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class DatabaseTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PM\Main\Database
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_instance = $this->getProvider()->get('PM\Main\Database');

        parent::setUp();
    }

    /**
     * Success test for get delete instance.
     *
     * @return void
     */
    public function testDeleteInstance() {
        $this->assertInstanceOf('\PM\Main\Database\Delete', $this->_instance->delete());
    }

    /**
     * Success test for get insert instance.
     *
     * @return void
     */
    public function testInsertInstance() {
        $this->assertInstanceOf('\PM\Main\Database\Insert', $this->_instance->insert());
    }

    /**
     * Success test for get update instance.
     *
     * @return void
     */
    public function testUpdateInstance() {
        $this->assertInstanceOf('\PM\Main\Database\Update', $this->_instance->update());
    }

    /**
     * Success test for get select instance.
     *
     * @return void
     */
    public function testSelectInstance() {
        $this->assertInstanceOf('\PM\Main\Database\Select', $this->_instance->select());
    }

    /**
     * Success test for get query instance.
     *
     * @return void
     */
    public function testQueryInstance() {
        $this->assertInstanceOf('\PM\Main\Database\Query', $this->_instance->query());
    }

    /**
     * Success test for get connection instance.
     *
     * @return void
     */
    public function testGetConnectionInstance() {
        $this->assertInstanceOf('\PM\Main\Database\Connection', $this->_instance->getConnection());
    }
}
