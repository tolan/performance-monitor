<?php

namespace PF\scripts\Install;

use PF\Main\Provider;
use PF\Main\Filesystem\Directory;
use PF\Main\Filesystem\File;

/**
 * This script defines a Manager class for run all the migrations that have not been started.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    scripts
 */
class Manager {

    /**
     * Singleton instance.
     *
     * @var Manager
     */
    private static $instance = null;

    /**
     * Provider instance.
     *
     * @var Provider
     */
    private $_provider;

    /**
     * Repository instance.
     *
     * @var Repository
     */
    private $_repository;

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     *
     * @return void
     */
    private function __construct(Provider $provider) {
        $this->_provider   = $provider;
        $this->_repository = $provider->get('PF\scripts\Install\Repository');
    }

    /**
     * Runs all migration that have not been started.
     *
     * @param \PF\Main\Provider $provider Provider instance.
     *
     * @return boolean
     */
    public static function run(Provider $provider) {
        if (self::$instance === null) {
            self::$instance = new self($provider);
        }

        self::$instance->_install();

        return true;
    }

    /**
     * Install part of run process. It takes all migrations and iterate over them.
     *
     * @return boolean
     */
    private function _install() {
        $dir    = new Directory(__DIR__.'/migrations');
        $files  = $dir->getAll();
        $result = true;

        try {
            foreach ($files as $item) { /* @var $item \PF\Main\Filesystem\File */
                if ($item instanceof File) {
                    $this->_doMigration($item);
                }
            }
        } catch (\Exception $exc) {
            $log = $this->_provider->get('PF\Main\Log'); /* @var $log \PF\Main\Log */
            $log->fatal($exc);
            $result = false;
        }

        return $result;
    }

    /**
     * It installs specific migration.
     *
     * @param \PF\Main\Filesystem\File $file File instance of migration
     *
     * @return boolean
     */
    private function _doMigration(File $file) {
        $name      = $file->getName();
        $installed = array();

        if ($this->_repository->isExistsDatabase()) {
            $installed = $this->_repository->findVersionByName($name);
        }

        if (count($installed) === 0) {
            $className = $this->_getClassName($name);
            include_once 'migrations/'.$name;

            $class = new $className($this->_provider); /* @var $class \PF\scripts\migrations\AbstractMigration */
            $class->run();
            $this->_repository->createVersion($name);
        }

        return true;
    }

    /**
     * Returns name of class for migration by file name.
     *
     * @param string $fileName File name of migration
     *
     * @return string
     */
    private function _getClassName($fileName) {
        $prefix = strstr($fileName, '_', true);

        $postfix = rtrim(substr($fileName, strlen($prefix) + 1), '.php');

        return 'PF\scripts\Install\migrations\\'.$postfix.'_'.$prefix;
    }
}