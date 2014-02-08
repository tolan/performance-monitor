<?php

namespace PF\Tests\Unit\Main\Translate;

use PF\Main\Abstracts\Unit\TestCase;

/**
 * This script defines class for php unit test case of class \PF\Main\Translate\Repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Tests
 */
class RepositoryTest extends TestCase {

    /**
     * Repository instance.
     *
     * @var \PF\Main\Translate\Repository
     */
    private $_repository;

    /**
     * Init method for create instances and basic setup.
     *
     * @return void
     */
    protected function setUp() {
        $this->_repository = $this->getProvider()->get('PF\Main\Translate\Repository');

        parent::setUp();
    }

    /**
     * Success test for get all translation.
     *
     * @return void
     */
    public function testGetAll() {
        $translate = $this->_repository->getTranslateTable();

        $this->assertCount(2, $translate);
    }

    /**
     * Success test for get all translation from CS lang.
     *
     * @return void
     */
    public function testGetAllCS() {
        $translate = $this->_repository->getTranslateTable('CS');

        $this->assertCount(3, $translate);
    }

    /**
     * Success test for get all translation from CS lang and module main.
     *
     * @return void
     */
    public function testGetModule() {
        $translate = $this->_repository->getTranslateTable('CS', 'main');

        $this->assertCount(1, $translate);
    }

    /**
     * Success test for get all translation from EN lang and module main.
     *
     * @return void
     */
    public function testGetModuleEN() {
        $translate = $this->_repository->getTranslateTable('EN', 'main');

        $this->assertCount(0, $translate);
    }
}
