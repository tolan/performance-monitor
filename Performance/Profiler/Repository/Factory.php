<?php

namespace PM\Profiler\Repository;

use PM\Main\Provider;
use PM\Main\Filesystem;
use PM\Main\Cache;
use PM\Profiler\Monitor\Enum\Type;

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
     * @var \PM\Main\Provider
     */
    private $_provider;

    /**
     * Construct method.
     *
     * @param \PM\Main\Provider $provider Provider instance
     *
     * @return void
     */
    public function __construct(Provider $provider) {
        $this->_provider = $provider;
    }

    /**
     * Returns instance repository for filters.
     *
     * @return \PM\Profiler\Repository\Filter
     */
    public function getFilter() {
        return $this->_provider->get('PM\Profiler\Repository\Filter');
    }

    /**
     * Returns instance repository for measures.
     *
     * @param string $type One of enum Type
     *
     * @return \PM\Profiler\Monitor\Interfaces\Repository
     *
     * @throws Exception
     */
    public function getMeasure($type, $measureId = null) {
        $repository = null;
        switch ($type) {
            case Type::MYSQL:
                $repository = $this->_provider->get('PM\Profiler\Repository\Measure\MySQL');
                break;
            case Type::SESSION:
                $driver = $this->_provider->get('PM\Main\Cache\Session'); /* @var $driver \PM\Main\Cache\Session */
                $cache = $this->_provider->prototype('PM\Main\Cache'); /* @var $cache \PM\Main\Cache */
                $cache->setDriver($driver);
                $repository = $this->_provider->get('PM\Profiler\Repository\Measure\Cache');
                /* @var $repository \PM\Profiler\Repository\Measure\Cache */
                $repository->setCache($cache);
                break;
            case Type::FILE:
                $repository = $this->_provider->get('PM\Profiler\Repository\Measure\Cache');
                /* @var $repository \PM\Profiler\Repository\Measure\Cache */
                $filepath   = $this->_provider->get('config')->getRoot().'/tmp/Profiler';
                $dir        = new Filesystem\Directory($filepath);

                /* Cache and cache driver has function adapter for communication between repository and concrete file. */
                if ($measureId !== null) {
                    // TODO extract file path to better place
                    $file        = $dir->fileExists($measureId) ? $dir->getFile($measureId) : $dir->createFile($measureId);
                    $file->open();
                    $cacheDriver = new Cache\File(Cache\File::DEFAULT_NAMESPACE, $this->_provider->get('config'), $file);
                    $cache       = $this->_provider->prototype('cache', true); /* @var $cache \PM\Main\Cache */
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
     * @return \PM\Profiler\Repository\Request
     */
    public function getRequest() {
        return $this->_provider->get('PM\Profiler\Repository\Request');
    }

    /**
     * Returns instance repository for scenarios.
     *
     * @return \PM\Profiler\Repository\Scenario
     */
    public function getScenario() {
        return $this->_provider->get('PM\Profiler\Repository\Scenario');
    }

    /**
     * Returns instance repository for tests.
     *
     * @return \PM\Profiler\Repository\Test
     */
    public function getTest() {
        return $this->_provider->get('PM\Profiler\Repository\Test');
    }
}
