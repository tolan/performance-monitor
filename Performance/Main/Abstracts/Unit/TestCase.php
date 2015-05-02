<?php

namespace PM\Main\Abstracts\Unit;

use PM\Main\Provider;

/**
 * Abstract class for php unit test case.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * https://phpunit.de/manual/current/en/appendixes.assertions.html
 * @method void assertArrayHasKey(mixed $key, array $array[, string $message = '']) Reports an error identified by $message if $array does not have the $key.
 * @method void assertClassHasAttribute(string $attributeName, string $className[, string $message = '']) Reports an error identified by $message if $className::attributeName does not exist.
 * @method void assertClassHasStaticAttribute(string $attributeName, string $className[, string $message = '']) Reports an error identified by $message if $className::attributeName does not exist.
 * @method void assertContains(mixed $needle, Iterator|array $haystack[, string $message = '']) Reports an error identified by $message if $needle is not an element of $haystack.
 * @method void assertContainsOnly(string $type, Iterator|array $haystack[, boolean $isNativeType = NULL, string $message = '']) Reports an error identified by $message if $haystack does not contain only variables of type $type.
 * @method void assertContainsOnlyInstancesOf(string $classname, Traversable|array $haystack[, string $message = '']) Reports an error identified by $message if $haystack does not contain only instances of class $classname.
 * @method void assertCount($expectedCount, $haystack[, string $message = '']) Reports an error identified by $message if the number of elements in $haystack is not $expectedCount.
 * @method void assertEmpty(mixed $actual[, string $message = '']) Reports an error identified by $message if $actual is not empty.
 * @method void assertEqualXMLStructure(DOMElement $expectedElement, DOMElement $actualElement[, boolean $checkAttributes = FALSE, string $message = '']) Reports an error identified by $message if the XML Structure of the DOMElement in $actualElement is not equal to the XML structure of the DOMElement in $expectedElement.
 * @method void assertEquals(mixed $expected, mixed $actual[, string $message = '']) Reports an error identified by $message if the two variables $expected and $actual are not equal.
 * @method void assertFalse(bool $condition[, string $message = '']) Reports an error identified by $message if $condition is TRUE.
 * @method void assertFileEquals(string $expected, string $actual[, string $message = '']) Reports an error identified by $message if the file specified by $expected does not have the same contents as the file specified by $actual.
 * @method void assertFileExists(string $filename[, string $message = '']) Reports an error identified by $message if the file specified by $filename does not exist.
 * @method void assertGreaterThan(mixed $expected, mixed $actual[, string $message = '']) Reports an error identified by $message if the value of $actual is not greater than the value of $expected.
 * @method void assertGreaterThanOrEqual(mixed $expected, mixed $actual[, string $message = '']) Reports an error identified by $message if the value of $actual is not greater than or equal to the value of $expected.
 * @method void assertInstanceOf($expected, $actual[, $message = '']) Reports an error identified by $message if $actual is not an instance of $expected.
 * @method void assertInternalType($expected, $actual[, $message = '']) Reports an error identified by $message if $actual is not of the $expected type.
 * @method void assertJsonFileEqualsJsonFile(mixed $expectedFile, mixed $actualFile[, string $message = '']) Reports an error identified by $message if the value of $actualFile does not match the value of $expectedFile.
 * @method void assertJsonStringEqualsJsonFile(mixed $expectedFile, mixed $actualJson[, string $message = '']) Reports an error identified by $message if the value of $actualJson does not match the value of $expectedFile.
 * @method void assertJsonStringEqualsJsonString(mixed $expectedJson, mixed $actualJson[, string $message = ''])  Reports an error identified by $message if the value of $actualJson does not match the value of $expectedJson.
 * @method void assertLessThan(mixed $expected, mixed $actual[, string $message = '']) Reports an error identified by $message if the value of $actual is not less than the value of $expected.
 * @method void assertLessThanOrEqual(mixed $expected, mixed $actual[, string $message = '']) Reports an error identified by $message if the value of $actual is not less than or equal to the value of $expected.
 * @method void assertNull(mixed $variable[, string $message = '']) Reports an error identified by $message if $variable is not NULL.
 * @method void assertObjectHasAttribute(string $attributeName, object $object[, string $message = '']) Reports an error identified by $message if $object->attributeName does not exist.
 * @method void assertRegExp(string $pattern, string $string[, string $message = '']) Reports an error identified by $message if $string does not match the regular expression $pattern.
 * @method void assertStringMatchesFormat(string $format, string $string[, string $message = '']) Reports an error identified by $message if the $string does not match the $format string.
 * @method void assertStringMatchesFormatFile(string $formatFile, string $string[, string $message = '']) Reports an error identified by $message if the $string does not match the contents of the $formatFile.
 * @method void assertSame(mixed $expected, mixed $actual[, string $message = '']) Reports an error identified by $message if the two variables $expected and $actual do not have the same type and value.
 * @method void assertStringEndsWith(string $suffix, string $string[, string $message = '']) Reports an error identified by $message if the $string does not end with $suffix.
 * @method void assertStringEqualsFile(string $expectedFile, string $actualString[, string $message = '']) Reports an error identified by $message if the file specified by $expectedFile does not have $actualString as its contents.
 * @method void assertStringStartsWith(string $prefix, string $string[, string $message = '']) Reports an error identified by $message if the $string does not start with $prefix.
 * @method void assertThat(mixed $value, PHPUnit_Framework_Constraint $constraint[, $message = '']) Reports an error identified by $message if the $value does not match the $constraint.
 * @method void assertTrue(bool $condition[, string $message = '']) Reports an error identified by $message if $condition is FALSE.
 * @method void assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile[, string $message = '']) assertXmlFileEqualsXmlFile(string $expectedFile, string $actualFile[, string $message = ''])
 * @method void assertXmlStringEqualsXmlFile(string $expectedFile, string $actualXml[, string $message = '']) Reports an error identified by $message if the XML document in $actualXml is not equal to the XML document in $expectedFile.
 * @method void assertXmlStringEqualsXmlString(string $expectedXml, string $actualXml[, string $message = '']) Reports an error identified by $message if the XML document in $actualXml is not equal to the XML document in $expectedXml.
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
        $this->getProvider()
            ->reset()
            ->set($config, 'config');

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
        $config
            ->reset()
            ->fromArray($this->_configData);
        $this->cleanCache();

        parent::tearDown();
    }

    /**
     * It cleans cache.
     *
     * @return TestCase
     */
    protected function cleanCache() {
        $this->getProvider()->get('cache')->clean();

        return $this;
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
                $columns = array();
                foreach ($yamlData as $row) {
                    $columns = array_unique(
                        array_merge(
                            array_keys($row),
                            $columns
                        )
                    );

                    if (isset($row['id'])) {
                        $data[$table][$row['id']] = $row;
                    } else {
                        $data[$table][] = $row;
                    }
                }

                foreach ($data[$table] as $key => $row) {
                    foreach (array_diff($columns, array_keys($row)) as $column) {
                        $row[$column] = null;
                    }

                    ksort($row);
                    $data[$table][$key] = $row;
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
        if (!empty($data)) {
            $database = $this->getProvider()->get('database'); /* @var $database \PM\Main\Database */

            try {
                foreach ($data as $table => $rows) {
                    $database->query()->execute('TRUNCATE '.$table);
                }

                foreach ($data as $table => $rows) {
                    $database->insert()->setTable($table)->massInsert($rows)->run();
                }
            } catch (\Exception $e) {
                $this->getProvider()->get('log')->error($e);
                throw $e;
            }
        }

        return $this;
    }
}
