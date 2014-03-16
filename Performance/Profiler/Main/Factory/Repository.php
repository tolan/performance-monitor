<?php

namespace PF\Profiler\Main\Factory;

class Repository extends AbstractFactory {

    public function getRepository() {
        $repository = null;

        switch ($this->_getType()) {
            case self::TYPE_MYSQL:
                $repository = $this->getProvider()->get('PF\Profiler\Main\Repository\MySQL');
                break;
            case self::TYPE_CACHE:
                $cache = $this->getProvider()->prototype('cache'); /* @var $cache \PF\Main\Cache */
                $cache->setDriver($this->getProvider()->get('PF\Main\Cache\File'));
                $repository = $this->getProvider()->get('PF\Profiler\Main\Repository\Cache');
                /* @var $repository \PF\Profiler\Main\Repository\Cache */
                $repository->setCache($cache);
                break;
            default:
                throw new Exception('Repository doesn\'t exist for '.$this->_getType().'.');
        }

        return $repository;
    }

    private function _getType() {
        // TODO implement get type by request params
        return self::TYPE_CACHE;
    }
}
