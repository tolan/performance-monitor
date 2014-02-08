<?php

namespace PF\Tests\Unit\Main\Database;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Database\Select.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class SelectTest extends TestCase {

    /**
     * database instance.
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
     * Success test for assemble of select instance.
     *
     * @return void
     */
    public function testAssemble() {
        $select = $this->_database->select()->from('test', 'col1');
        $this->assertEquals('SELECT test.col1 FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'), 'col1');
        $this->assertEquals('SELECT alias.col1 FROM test AS alias', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'), array('col1', 'col2'));
        $this->assertEquals('SELECT alias.col1, alias.col2 FROM test AS alias', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'), array('columnalias1' => 'col1', 'col2'));
        $this->assertEquals('SELECT alias.col1 AS columnalias1, alias.col2 FROM test AS alias', $select->assemble());

        $select = $this->_database->select()->from('test')->columns('col1');
        $this->assertEquals('SELECT test.*, col1 FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'))->columns('col1');
        $this->assertEquals('SELECT alias.*, col1 FROM test AS alias', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'))->columns(array('aliascol1' => 'col1'));
        $this->assertEquals('SELECT alias.*, col1 AS aliascol1 FROM test AS alias', $select->assemble());

        $select = $this->_database->select()->from('test')->distinct();
        $this->assertEquals('SELECT DISTINCT test.* FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from('test')->distinct()->group('test.id');
        $this->assertEquals('SELECT DISTINCT test.* FROM test AS test GROUP BY test.id', $select->assemble());

        $select = $this->_database->select()->from('test')->distinct()->group(array('test.id', 'test.name'));
        $this->assertEquals('SELECT DISTINCT test.* FROM test AS test GROUP BY test.id, test.name', $select->assemble());

        $select = $this->_database->select()->from('test')->distinct()->having('test.id = 1');
        $this->assertEquals('SELECT DISTINCT test.* FROM test AS test HAVING test.id = 1', $select->assemble());

        $where  = $this->_database->select()->createWhere()->where('id = ?', 1);
        $select = $this->_database->select()->from('test')->distinct()->having($where);
        $this->assertEquals('SELECT DISTINCT test.* FROM test AS test HAVING (id = 1)', $select->assemble());

        $select = $this->_database->select()->from(array('t1' => 'test'))->joinInner(array('t2' => 'test2'), 't2.id = t1.id');
        $this->assertEquals('SELECT t1.*, t2.* FROM test AS t1 INNER JOIN test2 AS t2 ON t2.id = t1.id', $select->assemble());

        $select = $this->_database->select()->from(array('t1' => 'test'))->joinLeft(array('t2' => 'test2'), 't2.id = t1.id');
        $this->assertEquals('SELECT t1.*, t2.* FROM test AS t1 LEFT JOIN test2 AS t2 ON t2.id = t1.id', $select->assemble());

        $select = $this->_database->select()->from('test')->where('id = ?', 1);
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id = 1)', $select->assemble());

        $select = $this->_database->select()->from('test')->where('id IN (?)', array(1, 2 ,3));
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id IN (1, 2, 3))', $select->assemble());

        $select = $this->_database->select()->from('test')->where('id IN (?)', array('first', 'second' ,'third'));
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id IN (\'first\', \'second\', \'third\'))', $select->assemble());

        $where  = $this->_database->select()->createWhere()->where('name = ?', 'joe')->orWhere('name = ?', 'mike');
        $select = $this->_database->select()->from('test')->where($where);
        $this->assertEquals('SELECT test.* FROM test AS test WHERE ((name = \'joe\') OR (name = \'mike\'))', $select->assemble());

        $select = $this->_database->select()->from('test')->orWhere('id = ?', 1);
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id = 1)', $select->assemble());

        $select = $this->_database->select()->from('test')->orWhere('id = ?', 1)->orWhere('id = ?', 2);
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id = \'1\') OR (id = \'2\')', $select->assemble());
    }

    /**
     * Success test for create new instance of where condition.
     *
     * @return void
     */
    public function testCreateWhere() {
        $where = $this->_database->select()->createWhere();

        $this->assertInstanceOf('PF\Main\Database\Where', $where);
    }

    /**
     * Success test for set selected columns.
     *
     * @return void
     */
    public function testColumns() {
        $select = $this->_database->select()->from('test')->columns(array('alias' => 'col1'));

        $this->assertEquals('SELECT test.*, col1 AS alias FROM test AS test', $select->assemble());
    }

    /**
     * Success test for set distinct function.
     *
     * @return void
     */
    public function testDistinct() {
        $select = $this->_database->select()->from('test')->distinct();

        $this->assertEquals('SELECT DISTINCT test.* FROM test AS test', $select->assemble());
    }

    /**
     * Success test for set source table.
     *
     * @return void
     */
    public function testFrom() {
        $select = $this->_database->select()->from('test');
        $this->assertEquals('SELECT test.* FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'));
        $this->assertEquals('SELECT alias.* FROM test AS alias', $select->assemble());

        $select = $this->_database->select()->from('test', 'col1');
        $this->assertEquals('SELECT test.col1 FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from('test', array('col1', 'col2'));
        $this->assertEquals('SELECT test.col1, test.col2 FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from('test', array('aliasCol1' => 'col1', 'col2'));
        $this->assertEquals('SELECT test.col1 AS aliasCol1, test.col2 FROM test AS test', $select->assemble());

        $select = $this->_database->select()->from(array('alias' => 'test'), array('aliasCol1' => 'col1', 'col2'));
        $this->assertEquals('SELECT alias.col1 AS aliasCol1, alias.col2 FROM test AS alias', $select->assemble());
    }

    /**
     * Success test for get binded values.
     *
     * @return void
     */
    public function testGetBind() {
        $bind = $this->_database->select()->from('test')->where('id = ?', 1)->getBind();
        $this->assertEquals(array(1), $bind);

        $bind = $this->_database->select()->from('test')->where('id = ?', array(1, 2, 3))->getBind();
        $this->assertEquals(array(1, 2, 3), $bind);

        $bind = $this->_database->select()->from('test')->where('id = ?', array('text1', 'test2', 'foo3'))->getBind();
        $this->assertEquals(array('text1', 'test2', 'foo3'), $bind);
    }

    /**
     * Success test for get created statement.
     *
     * @return void
     */
    public function testGetStatement() {
        $statement = $this->_database->select()->from('test')->where('id = ?', 1)->getStatement();
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id = ?)', $statement);

        $statement = $this->_database->select()->from('test')->where('id IN (?)', array(1, 2))->orWhere('name LIKE ?', '%testName%')->getStatement();
        $this->assertEquals('SELECT test.* FROM test AS test WHERE (id IN (?)) OR (name LIKE ?)', $statement);
    }

    /**
     * Success test for group function.
     *
     * @return void
     */
    public function testGroup() {
        $select = $this->_database->select()->from('test')->group('id');
        $this->assertEquals('SELECT test.* FROM test AS test GROUP BY id', $select->assemble());

        $select = $this->_database->select()->from('test')->group(array('id', 'name'));
        $this->assertEquals('SELECT test.* FROM test AS test GROUP BY id, name', $select->assemble());
    }

    /**
     * Success test for having function.
     *
     * @return void
     */
    public function testHaving() {
        $select = $this->_database->select()->from('test')->having('id = 1');
        $this->assertEquals('SELECT test.* FROM test AS test HAVING id = 1', $select->assemble());

        $where  = $this->_database->select()->createWhere()->where('id = ?', 2);
        $select = $this->_database->select()->from('test')->having($where);
        $this->assertEquals('SELECT test.* FROM test AS test HAVING (id = 2)', $select->assemble());
    }

    /**
     * Success test for join table with inner method.
     *
     * @return void
     */
    public function testJoinInner() {
        $select = $this->_database->select()->from('test')->joinInner('table', 'table.id = test.id');
        $this->assertEquals('SELECT test.*, table.* FROM test AS test INNER JOIN table AS table ON table.id = test.id', $select->assemble());

        $select = $this->_database->select()->from(array('t1' => 'test'))->joinInner('table', 'table.id = t1.id');
        $this->assertEquals('SELECT t1.*, table.* FROM test AS t1 INNER JOIN table AS table ON table.id = t1.id', $select->assemble());

        $select = $this->_database->select()->from(array('t1' => 'test'))->joinInner(array('t2' => 'table'), 't2.id = t1.id');
        $this->assertEquals('SELECT t1.*, t2.* FROM test AS t1 INNER JOIN table AS t2 ON t2.id = t1.id', $select->assemble());

        $where  = $this->_database->select()->createWhere()->where('t1.id IN (?)', array(1, 2, 3))->assemble();
        $select = $this->_database->select()->from(array('t1' => 'test'))->joinInner(array('t2' => 'table'), 't2.id = t1 AND '.$where);
        $this->assertEquals('SELECT t1.*, t2.* FROM test AS t1 INNER JOIN table AS t2 ON t2.id = t1 AND (t1.id IN (1, 2, 3))', $select->assemble());
    }

    /**
     * Success test for join table with left method.
     *
     * @return void
     */
    public function testJoinLeft() {
        $select = $this->_database->select()->joinLeft(array('t2' => 'test'), 't1.id = t2.id')->from(array('t1' => 'table'));
        $this->assertEquals('SELECT t2.*, t1.* FROM table AS t1 LEFT JOIN test AS t2 ON t1.id = t2.id', $select->assemble());

        $where  = $this->_database->select()->createWhere()->where('t1.name LIKE ?', '%te\'st\Name%');
        $select = $this->_database->select()->from(array('t1' => 'test'))->joinInner(array('t2' => 'table'), 't2.id = t1 AND '.$where);
        $this->assertEquals(
            'SELECT t1.*, t2.* FROM test AS t1 INNER JOIN table AS t2 ON t2.id = t1 AND (t1.name LIKE \'%te\\\'st\\\\Name%\')',
            $select->assemble()
        );
    }

    /**
     * Success test for set where condition.
     *
     * @return void
     */
    public function testWhere() {
        $select = $this->_database->select()->from('table')->where('count = ?', 1);
        $this->assertEquals('SELECT table.* FROM table AS table WHERE (count = 1)', $select->assemble());

        $where  = $this->_database->select()->createWhere()->where('id = ?', 1)->where('count > ?', 5);
        $where2 = $this->_database->select()->createWhere()->where('id = ?', 2)->orWhere('count < ?', 10);
        $select = $this->_database->select()->from('table')->where($where)->where($where2)->orWhere('count = ?', 1);

        $this->assertEquals(
            'SELECT table.* FROM table AS table WHERE ((id = \'1\') AND (count > \'5\')) AND ((id = \'2\') OR (count < \'10\')) OR (count = 1)',
            $select->assemble()
        );
    }

}
