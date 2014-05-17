<?php

namespace PF\Main\Cache;

use PF\Main\Config;
use PF\Main\Filesystem;

/**
 * This script defines driver class for cache which save data to file.
 * File is persistent over all requests and sessions.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class File extends AbstractDriver implements Interfaces\Driver {

    /**
     * Default dirname for cache files.
     */
    const TMP_DIR = 'tmp';

    /**
     * File instance.
     *
     * @var \PF\Main\Filesystem\File
     */
    private $_file = null;

    /**
     * Construct method. It require file or config instance.
     *
     * @param \PF\Main\Filesystem\File $file      File instance for caching
     * @param \PF\Main\Config          $config    Config instance for get parameters when file is not set
     * @param string                   $namespace Default naemespace which is used for new filename
     *
     * @return void
     */
    public function __construct(Filesystem\File $file = null, Config $config = null, $namespace = self::DEFAULT_NAMESPACE) {
        if ($file === null) {
            $tmpDir = is_dir($config->get('tmpDir')) ? $config->get('tmpDir') : $config->get('root').'/'.self::TMP_DIR;
            $file   = new Filesystem\File($tmpDir, $namespace.'.CACHE.tmp', true, true);
        }

        $this->_file = $file;
    }

    /**
     * Load variable from cache.
     *
     * @param string $name Name of variable
     *
     * @return mixed
     *
     * @throws \PF\Main\Cache\Exception Throws when variable is not defined
     */
    public function load($name = null) {
        if (array_key_exists($name, $this->_data) === false) {
            while(($line = $this->_file->fgets()) && !isset($this->_data[$name])) {
                $data = unserialize(json_decode($line));
                foreach ($data as $key => $value) {
                    $this->_data[$key] = $value;
                }
            }
        }

        return parent::load($name);
    }

    /**
     * Sets value to variable by name.
     *
     * @param string $name  Name of variable
     * @param mixed  $value Value for save
     *
     * @return \PF\Main\Cache\File
     */
    public function save($name, $value) {
        if (array_key_exists($name, $this->_data) === true) {
            unset($this->_data[$name]);
        }

        $serialized = serialize(array($name => $value));

        $this->_file->fputs(json_encode($serialized)."\r\n");

        return $this;
    }

    /**
     * Returns that variable is set.
     *
     * @param string $name Name of variable
     *
     * @return boolean
     */
    public function has($name) {
        $this->load();

        return parent::has($name);
    }

    /**
     * Clean variable by name.
     *
     * @param string $name Name of variable
     *
     * @return \PF\Main\Cache\File
     *
     * @throws \PF\Main\Cache\Exception Throws when variable is not set.
     */
    public function clean($name = null) {
        if ($name === null) {
            $this->_file->delete()->open(true);
        } else {
            //TODO clean by name
            throw new Exception('Variable with name "'.$name.'" cannot be removed.');
        }

        return parent::clean($name);
    }
}
