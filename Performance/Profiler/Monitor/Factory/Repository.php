<?php

namespace PF\Profiler\Monitor\Factory;

use PF\Profiler\Monitor\Enum\Type;
use PF\Profiler\Monitor\Enum\HttpKeys;
use PF\Profiler\Monitor\Exception;
use PF\Main\Cache;
use PF\Main\Filesystem;

/**
 * This script defines factory class for monitor repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Repository extends AbstractFactory {

    /**
     * Define default type for repository
     */
    const DEFAULT_TYPE = Type::SESSION;

    /**
     * Returns instance of monitor repository.
     *
     * @param enum  $type   One of enum \PF\Profiler\Monitor\Enum\Type
     * @param array $params Request parameters
     *
     * @return \PF\Profiler\Monitor\Repository\AbstractRepository
     *
     * @throws Exception Throws when repository is not defined
     */
    public function getRepository($type = null, $params = null) {
        $type       = $type   === null ? $this->getType()   : $type;
        $params     = $params === null ? $this->getParams() : $params;
        $repository = null;

        switch ($type) {
            case Type::SESSION:
                $repository = $this->_getSessionRepository();
                break;
            case Type::MYSQL:
                $repository = $this->_getMySQLRepository($params);
                break;
            case Type::FILE:
                $repository = $this->_getFileRepository($params);
                break;
            default:
                throw new Exception('Repository doesn\'t exist for "'.$this->getType().'".');
        }
        /* @var $repository \PF\Profiler\Monitor\Interfaces\Repository */

        if (isset($params[HttpKeys::REQUEST_ID])) {
            $filterRepos = $this->getProvider()->get('PF\Profiler\Repository\Filter');
            $repository->setFilterRepository($filterRepos, $params[HttpKeys::REQUEST_ID]);
        }

        return $repository;
    }

    /**
     * Returns instance of MySQL repository.
     *
     * @param array $params Request parameters
     *
     * @return \PF\Profiler\Monitor\Repository\MySQL
     */
    private function _getMySQLRepository($params = null) {
        $repository = $this->getProvider()->get('PF\Profiler\Monitor\Repository\MySQL');
        /* @var $repository \PF\Profiler\Monitor\Repository\MySQL */
        $repository->setMeasureId($params[HttpKeys::MEASURE_ID]);

        return $repository;
    }

    /**
     * Returns instance of File repository.
     *
     * @param array $params Request parameters
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    private function _getFileRepository($params = null) {
        $filepath    = $this->getProvider()->get('config')->getRoot().'/tmp/Profiler';
        // TODO extract file path to better place
        $file        = new Filesystem\File($filepath, $params[HttpKeys::MEASURE_ID], true, true);
        $cacheDriver = new Cache\File($file);
        $cache       = $this->getProvider()->prototype('cache'); /* @var $cache \PF\Main\Cache */
        $cache->setDriver($cacheDriver);

        /* Cache and cache driver has function adapter for communication between repository and concrete file. */

        $repository = $this->getProvider()->get('PF\Profiler\Monitor\Repository\Cache');
        /* @var $repository \PF\Profiler\Monitor\Repository\Cache */
        $repository->setCache($cache);

        return $repository;
    }

    /**
     * Returns instance of Session repository.
     *
     * @param array $params Request parameters
     *
     * @return \PF\Profiler\Monitor\Repository\Cache
     */
    private function _getSessionRepository() {
        $cache      = $this->getProvider()->prototype('cache'); /* @var $cache \PF\Main\Cache */
        $repository = $this->getProvider()->get('PF\Profiler\Monitor\Repository\Cache');
        /* @var $repository \PF\Profiler\Monitor\Repository\Cache */
        $repository->setCache($cache);

        return $repository;
    }
}
