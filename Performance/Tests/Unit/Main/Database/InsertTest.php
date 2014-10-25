<?php

namespace PM\Tests\Unit\Main\Database;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Database\Insert.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class InsertTest extends TestCase {

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
     * Success test for assemble of insert instance.
     *
     * @return void
     */
    public function testAssemble() {
        $insert = $this->_database->insert()->setTable('test')->setInsertData(array('col' => 'val'));
        $this->assertEquals('INSERT INTO test (`col`) VALUES (\'val\')', $insert->assemble());

        $massInsert = array(
            array('col' => 'val1'),
            array('col' => 'val2')
        );

        $insert = $this->_database->insert()->setTable('test')->massInsert($massInsert);
        $this->assertEquals('INSERT INTO test (`col`) VALUES (\'val1\'), (\'val2\')', $insert->assemble());
    }

    /**
     * Success test for insert one row.
     *
     * @return void
     */
    public function testInsertOneRow() {
        $original = $this->_database->select()->from('measure')->fetchAll();

        $insertId = $this->_database->insert()->setTable('measure')->setInsertData(array('name' => 'test'))->run();

        $insertedAll = $this->_database->select()->from('measure')->fetchAll();
        $inserted    = $this->_database->select()->from('measure')->where('id = ?', $insertId)->fetchOne();
        $expected = array(
            'id'          => $insertId,
            'name'        => 'test',
            'description' => '',
            'edited'      => ''
        );

        $this->assertEquals($expected, $inserted);
        $this->assertEquals(1, count($insertedAll) - count($original));
    }

    /**
     * Success test for insert three rows in one statement.
     *
     * @return void
     */
    public function testInsertMultiRow() {
        $original = $this->_database->select()->from('measure')->fetchAll();

        $data = array(
            array('name' => 'multi row 1'),
            array('name' => 'multi row 2'),
            array('name' => 'multi row 3')
        );
        $this->_database->insert()->setTable('measure')->massInsert($data)->run();

        $insertedAll = $this->_database->select()->from('measure')->fetchAll();
        $this->assertEquals(3, count($insertedAll) - count($original));
    }

    /**
     * Success test for get insert id.
     *
     * @return void
     */
    public function testGetId() {
        $original = $this->_database->select()->from('measure')->fetchAll();
        $insert = $this->_database->insert()->setTable('measure')->setInsertData(array('name' => 'test'));

        $insertId = $insert->run();
        $id       = $insert->getId();

        $this->assertEquals($insertId, $id);
        $this->assertEquals(count($original) + 1, $id);
    }

    /**
     * Success test for get statement of insert instance.
     *
     * @return void
     */
    public function testGetStatement() {
        $insert = $this->_database->insert()->setTable('test')->setInsertData(array('col' => 'val'));
        $this->assertEquals('INSERT INTO test (`col`) VALUES (?)', $insert->getStatement());
    }

    /**
     * Success test for get bind of insert instance.
     *
     * @return void
     */
    public function testGetBind() {
        $bind = array('col' => 'val');
        $insert = $this->_database->insert()->setTable('test')->setInsertData($bind);
        $this->assertEquals(array_values($bind), $insert->getBind());
    }
}
