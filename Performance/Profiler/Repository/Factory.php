<?php

namespace PF\Profiler\Repository;

use PF\Main\Provider;
use PF\Main\Filesystem;
use PF\Main\Cache;
use PF\Profiler\Monitor\Enum\Type;

/**
 * This script defines factory class for repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Factory {

    /**
     * Provider instance.
     *
     * @var \PF\Main\Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Returns instance repository for filters.
     *
     * @return \PF\Profiler\Repository\Filter
     */
    public function getFilter() {
        return $this->_provider->get('PF\Profiler\Repository\Filter');
    }

    /**
     * Returns instance repository for measures.
     *
     * @param string $type One of enum Type
     *
     * @return \PF\Profiler\Monitor\Interfaces\Repository
     *
     * @throws Exception
     */
    public function getMeasure($type, $measureId = null) {
        $repository = null;
        switch ($type) {
            case Type::MYSQL:
                $repository = $this->_provider->get('PF\Profiler\Repository\Measure\MySQL');
                break;
            case Type::SESSION:
                $driver = $this->_provider->get('PF\Main\Cache\Session'); /* @var $driver \PF\Main\Cache\Session */
                $cache = $this->_provider->prototype('PF\Main\Cache'); /* @var $cache \PF\Main\Cache */
                $cache->setDriver($driver);
                $repository = $this->_provider->get('PF\Profiler\Repository\Measure\Cache');
                /* @var $repository \PF\Profiler\Repository\Measure\Cache */
                $repository->setCache($cache);
                break;
            case Type::FILE:
                $repository = $this->_provider->get('PF\Profiler\Repository\Measure\Cache');
                /* @var $repository \PF\Profiler\Repository\Measure\Cache */
                $filepath   = $this->_provider->get('config')->getRoot().'/tmp/Profiler';
                $dir        = new Filesystem\Directory($filepath);

                /* Cache and cache driver has function adapter for communication between repository and concrete file. */
                if ($measureId !== null) {
                    // TODO extract file path to better place
                    $file        = $dir->fileExists($measureId) ? $dir->getFile($measureId) : $dir->createFile($measureId);
                    $file->open();
                    $cacheDriver = new Cache\File($file);
                    $cache       = $this->_provider->prototype('cache'); /* @var $cache \PF\Main\Cache */
                    $cache->setDriver($cacheDriver);
                    $repository->setCache($cache);
                }

                $repository->setDir($dir);
                break;
        }

        return $repository;
    }

    /**
     * Returns instance repository for scenarios.
     *
     * @return \PF\Profiler\Repository\Request
     */
    public function getRequest() {
        return $this->_provider->get('PF\Profiler\Repository\Request');
    }

    /**
     * Returns instance repository for scenarios.
     *
     * @return \PF\Profiler\Repository\Scenario
     */
    public function getScenario() {
        return $this->_provider->get('PF\Profiler\Repository\Scenario');
    }

    /**
     * Returns instance repository for tests.
     *
     * @return \PF\Profiler\Repository\Test
     */
    public function getTest() {
        return $this->_provider->get('PF\Profiler\Repository\Test');
    }
}
