<?php

namespace PM\Main\Abstracts\Unit;

use PM\Main\Provider;

/**
 * Abstract class for php unit test case.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
abstract class TestCase extends \PHPUnit_Framework_TestCase {

    protected $backupGlobals = FALSE;

    /**
     * Provider instance.
     *
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * Backup for configuration
     *
     * @var array
     */
    private $_configData = array();

    /**
     * Construct method. Override and call parent method.
     *
     * @param  string $name
     * @param  array  $data
     * @param  string $dataName
     */
    public function __construct($name = NULL, array $data = array(), $dataName = '') {
        $this->_provider = Provider::getInstance();

        $this->_loadFixtures();

        parent::__construct($name, $data, $dataName);
    }

    /**
     * Method for initialize some attributes.
     *
     * @return void
     */
    protected function setUp() {
        $this->_loadFixtures();

        $config = $this->getProvider()->get('config');
        $this->getProvider()->reset()->set($config, 'config');

        $this->_configData = $config->toArray();

        parent::setUp();
    }

    /**
     * Method for make some operation after each test
     *
     * @return void
     */
    protected function tearDown() {
        $config = $this->getProvider()->get('config');
        $config->fromArray($this->_configData);

        parent::tearDown();
    }

    /**
     * Returns provider instance.
     *
     * @return \PM\Main\Provider
     */
    protected function getProvider() {
        return $this->_provider;
    }

    /**
     * It loads data from fixtures files to database.
     *
     * @return \PM\Main\Abstracts\Unit\TestCase
     */
    private function _loadFixtures() {
        $root      = $this->_provider->get('config')->get('root');
        $namespace = $this->_provider->get('config')->get('namespace');
        $class     = get_called_class();
        $classDir  = dirname($root.ltrim(str_replace('\\', '/', $class), $namespace));

        $fixtureFiles = $this->_findFixtureFiles($classDir, $root.'/Tests/Unit');
        $data         = array();

        foreach ($fixtureFiles as $file) {
            $yaml = yaml_parse_file($file);

            foreach ($yaml as $table => $yamlData) {
                foreach ($yamlData as $row) {
                    if (isset($row['id'])) {
                        $data[$table][$row['id']] = $row;
                    } else {
                        $data[$table][] = $row;
                    }
                }
            }
        }

        $this->_insertFixturesData($data);

        return $this;
    }

    /**
     * Find fixtures files from actual dir up to root dir.
     *
     * @param string $classDir Path to actual dir
     * @param string $rootDir  Path to root dir
     *
     * @return array
     */
    private function _findFixtureFiles($classDir, $rootDir) {
        $files = array();

        while(strlen($classDir) >= strlen($rootDir)) {
            $filename = $classDir.'/fixtures.yml';

            if (file_exists($filename)) {
                $files[] = $filename;
            }

            $classDir = dirname($classDir);
        }

        return array_reverse($files);
    }

    /**
     * It deletes database and loads new data.
     *
     * @param array $data Data for load. Array('table name' => rows)
     *
     * @return \PM\Main\Abstracts\Unit\TestCase
     */
    private function _insertFixturesData($data) {
        $database = $this->getProvider()->get('database'); /* @var $database \PM\Main\Database */

        foreach ($data as $table => $rows) {
            $database->delete()->setTable($table)->run();
            $database->query()->execute('ALTER TABLE '.$table.' AUTO_INCREMENT=1');
        }

        foreach ($data as $table => $rows) {
            $database->insert()->setTable($table)->massInsert($rows)->run();
        }

        return $this;
    }
}
