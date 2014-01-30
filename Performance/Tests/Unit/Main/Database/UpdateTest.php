<?php

namespace PF\Tests\Unit\Main\Database;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Database\Update.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class UpdateTest extends TestCase {

    /**
     * Access instance.
     *
     * @var \PF\Main\Database
     */
    private $_database;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_database = $this->getProvider()->get('PF\Main\Database');

        parent::setUp();
    }

    /**
     * Success test for assemble of update instance.
     *
     * @return void
     */
    public function testAssemble() {
        $update = $this->_database->update()->setTable('test')->setUpdateData(array('col' => 'val'));
        $this->assertEquals('UPDATE test AS test SET col = \'val\'', $update->assemble());

        $update = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('col' => 'val'));
        $this->assertEquals('UPDATE test AS alias SET col = \'val\'', $update->assemble());

        $update = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'));
        $this->assertEquals('UPDATE test AS alias SET alias.col = \'val\'', $update->assemble());

        $update = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'))->where('id = ?', 1);
        $this->assertEquals('UPDATE test AS alias SET alias.col = \'val\' WHERE (id = 1)', $update->assemble());

        $update = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'))
                ->where('id = ?', 1)->orWhere('name IN (:names)', array(':names' => array('test', 'aaa')));
        $this->assertEquals('UPDATE test AS alias SET alias.col = \'val\' WHERE (id = 1) OR (name IN (\'test\', \'aaa\'))', $update->assemble());
    }

    /**
     * Success test for get statement from update instance.
     *
     * @return void
     */
    public function testGetStatement() {
        $update = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'))->where('id = ?', 1);
        $this->assertEquals('UPDATE test AS alias SET alias.col = :alias.col WHERE (id = ?)', $update->getStatement());

        $update = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'))
                ->where('id = ?', 1)->orWhere('name IN (:names)', array(':names' => array('test', 'aaa')));
        $this->assertEquals('UPDATE test AS alias SET alias.col = :alias.col WHERE (id = ?) OR (name IN (:names))', $update->getStatement());
    }

    /**
     * Success test for get bind from update instance.
     *
     * @return void
     */
    public function testGetBind() {
        $update   = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'))->where('id = ?', 1);
        $expected = array(
            1,
            ':alias.col' => 'val'
        );
        $this->assertEquals($expected, $update->getBind());

        $update   = $this->_database->update()->setTable(array('alias' => 'test'))->setUpdateData(array('alias.col' => 'val'))
                ->where('id = ?', 1)->orWhere('name IN (:names)', array(':names' => array('test', 'aaa')));
        $expected = array(
            1,
            ':names' => array('test', 'aaa'),
            ':alias.col' => 'val'
        );
        $this->assertEquals($expected, $update->getBind());
    }

    /**
     * Success test for run of update instance.
     *
     * @return void
     */
    public function testRun() {
        $data = $this->_database->select()->from('measure')->where('id = ?', 1)->fetchOne();
        $this->assertEquals('first test', $data['name']);

        $this->_database->update()->setTable('measure')->setUpdateData(array('name' => 'updated name'))->where('id = :id', array(':id' => 1))->run();

        $data = $this->_database->select()->from('measure')->where('id = ?', 1)->fetchOne();
        $this->assertEquals('updated name', $data['name']);
    }
}
