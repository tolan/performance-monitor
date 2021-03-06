<?php

namespace PM\Tests\Unit\Main\Database;

use PM\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PM\Main\Database\Delete.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class DeleteTest extends TestCase {

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
     * Success test for assemble of delete instance.
     *
     * @return void
     */
    public function testAssemble() {
        $delete = $this->_database->delete();

        $delete->setTable('test');
        $this->assertEquals('DELETE FROM test', $delete->assemble());

        $delete->where('col1 IN (?)', array('aa', 'bb', 5));
        $this->assertEquals('DELETE FROM test WHERE (col1 IN (\'aa\', \'bb\', 5))', $delete->assemble());

        $delete->orWhere('test IN (:values)', array(':values' => array('cc', 'dd')));
        $expected = 'DELETE FROM test WHERE (col1 IN (\'aa\', \'bb\', 5)) OR (test IN (\'cc\', \'dd\'))';
        $this->assertEquals($expected, $delete->assemble());
    }

    /**
     * Success test for delete one row by where condition.
     *
     * @return void
     */
    public function testDeleteOneRow() {
        $delete   = $this->_database->delete();
        $original = $this->_database
            ->select()
            ->from('version')
            ->fetchAll();
        $first    = current($original);

        $delete->setTable('version')
            ->where('id = ?', $first['id'])
            ->run();
        $deleted = $this->_database
            ->select()
            ->from('version')
            ->fetchAll();

        $this->assertEquals(1, count($original) - count($deleted));
    }

    /**
     * Success test for delete all rows without where condition.
     *
     * @return void
     */
    public function testDeleteAllRows() {
        $delete = $this->_database->delete();

        $delete->setTable('version')->run();
        $deleted = $this->_database
            ->select()
            ->from('version')
            ->fetchAll();

        $this->assertCount(0, $deleted);
    }

    /**
     * Success test for get bind of delete instance.
     *
     * @return void
     */
    public function testGetBind() {
        $bind = array('test', 'test2', 'bind' => 'data');
        $delete = $this->_database
            ->delete()
            ->setTable('table')
            ->where('aa = ?', $bind);

        $this->assertEquals($bind, $delete->getBind()) ;
    }

    /**
     * Success test for create statement of delete instance.
     *
     * @return void
     */
    public function testGetStatement() {
        $delete = $this->_database
            ->delete()
            ->setTable('table')
            ->where('id = ?', 'aaa');

        $this->assertEquals('DELETE FROM table WHERE (id = ?)', $delete->getStatement());
    }
}
