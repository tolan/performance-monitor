<?php

namespace PF\Tests\Unit\Main;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Database.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class DatabaseTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Database
     */
    private $_instance;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_instance = $this->getProvider()->get('PF\Main\Database');

        parent::setUp();
    }

    /**
     * Success test for get delete instance.
     *
     * @return void
     */
    public function testDeleteInstance() {
        $this->assertInstanceOf('\PF\Main\Database\Delete', $this->_instance->delete());
    }

    /**
     * Success test for get insert instance.
     *
     * @return void
     */
    public function testInsertInstance() {
        $this->assertInstanceOf('\PF\Main\Database\Insert', $this->_instance->insert());
    }

    /**
     * Success test for get update instance.
     *
     * @return void
     */
    public function testUpdateInstance() {
        $this->assertInstanceOf('\PF\Main\Database\Update', $this->_instance->update());
    }

    /**
     * Success test for get select instance.
     *
     * @return void
     */
    public function testSelectInstance() {
        $this->assertInstanceOf('\PF\Main\Database\Select', $this->_instance->select());
    }

    /**
     * Success test for get query instance.
     *
     * @return void
     */
    public function testQueryInstance() {
        $this->assertInstanceOf('\PF\Main\Database\Query', $this->_instance->query());
    }

    /**
     * Success test for get connection instance.
     *
     * @return void
     */
    public function testGetConnectionInstance() {
        $this->assertInstanceOf('\PF\Main\Database\Connection', $this->_instance->getConnection());
    }
}
