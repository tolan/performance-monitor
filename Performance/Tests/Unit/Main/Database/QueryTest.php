<?php

namespace PM\Tests\Unit\Main\Database;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Database\Query.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class QueryTest extends TestCase {

    /**
     * database instance.
     *
     * @var \PM\Main\Database
     */
    private $_database;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_database = $this->getProvider()->get('PM\Main\Database');

        parent::setUp();
    }

    /**
     * Success test for assemble of query instance.
     *
     * @return void
     */
    public function testAssemble() {
        $query = $this->_database
            ->query()
            ->setSQL('AAA')
            ->assemble();

        $this->assertEquals('AAA', $query);
    }

    /**
     * Success test for execute SQL statement.
     *
     * @return void
     */
    public function testExecute() {
        $data = $this->_database
            ->query()
            ->execute('SELECT * FROM version')
            ->fetchAll();

        $this->assertCount(3, $data);
    }

    /**
     * Success test for execute and bind of SQL statement.
     *
     * @return void
     */
    public function testExecuteAndBind() {
        $data = $this->_database
            ->query()
            ->execute('SELECT * FROM version WHERE id = ?', array(1))
            ->fetchAll();

        $this->assertCount(1, $data);

        $data = $this->_database
            ->query()
            ->execute('SELECT * FROM version WHERE id = :id', array(':id' => 1))
            ->fetchAll();

        $this->assertCount(1, $data);
    }

    /**
     * Success test for fetch all rows from SQL statement.
     *
     * @return void
     */
    public function testFetchAll() {
        $data = $this->_database
            ->query()
            ->setSQL('SELECT * FROM version')
            ->fetchAll();

        $this->assertCount(3, $data);
    }

    /**
     * Success test for fetch one (first) row from SQL statement.
     *
     * @return void
     */
    public function testFetchOne() {
        $data = $this->_database
            ->query()
            ->setSQL('SELECT * FROM version')
            ->fetchOne();

        $expected = array(
            'id'        => 1,
            'name'      => 'first test',
            'installed' => '0000-00-00 00:00:00'
        );
        $this->assertEquals($expected, $data);
    }
}
