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
     * These constants define type of saved data.
     */
    const TYPE_OBJECT = 'object';
    const TYPE_OTHER  = 'array';

    /**
     * Define treshold limit. When count of unsaved data reach this limit then it run commit process.
     */
    const COMMIT_TRESHOLD = 100;

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
     * It means that the file is whole loaded.
     *
     * @var boolean
     */
    private $_fullLoaded = false;

    /**
     * Scope for unsaved data.
     *
     * @var array
     */
    private $_unsaved = array();

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
        if(array_key_exists($name, $this->_unsaved) === true) {
            $this->_data[$name] = $this->_unsaved[$name];
        } elseif (array_key_exists($name, $this->_data) === false && !$this->_fullLoaded) {
            if ($this->_file->isOpen() === false) {
                $this->_file->open(true);
            }

            if ($name === null) {
                $this->_loadAll();
            } else {
                $this->_loadBlock($name);
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

        $this->_unsaved[$name] = $value;

        if (count($this->_unsaved) > self::COMMIT_TRESHOLD) {
            $this->commit();
        }

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
        $result = false;
        if (array_key_exists($name, $this->_unsaved) === true) {
            $result = true;
        } else {
            $result = parent::has($name);
        }

        if ($result === false) {
            $this->load($name);
            $result = parent::has($name);
        }

        return $result;
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
            if ($this->_file->fileExist() === true) {
                $this->_file->delete();
            }

            $this->_unsaved = array();
        } else {
            //TODO clean by name
            throw new Exception('Variable with name "'.$name.'" cannot be removed.');
        }

        return parent::clean($name);
    }

    /**
     * Flush unsaved data to storage.
     *
     * @return \PF\Main\Cache\File
     */
    public function commit() {
        $this->_saveToFile($this->_unsaved);
        $this->_unsaved = array();

        return $this;
    }

    /**
     * Destruct method for clean storage.
     *
     * @return void
     */
    public function __destruct() {
        $this->commit();
        $this->_data = array();
    }

    /**
     * This loads all data from file to storage.
     *
     * @return \PF\Main\Cache\File
     */
    private function _loadAll() {
        $file  = $this->_file->file();
        $count = count($file);
        $data  = array();

        for($i = 0; $i < $count; $i++) {
            $dataLine  = json_decode($file[$i], true);
            $dataCount = count($dataLine);
            unset($file[$i]);

            for($j = 0; $j < $dataCount; $j++) {
                $item = $dataLine[$j];
                unset($dataLine[$j]);
                if ($item['type'] === self::TYPE_OBJECT) {
                    $item['value'] = unserialize($item['value']);
                }

                $data[$item['key']] = $item['value'];
            }
        }

        unset($file);
        $this->_data       = $data;
        $this->_fullLoaded = true;

        return $this;
    }

    /**
     * It loads block data from file where is variable with name.
     *
     * @param string $name Identificator of variable
     *
     * @return \PF\Main\Cache\File
     */
    private function _loadBlock($name) {
        while(($line = $this->_file->fgets()) && !isset($this->_data[$name])) {
            $dataLine = json_decode($line, true);
            unset($line);

            foreach ($dataLine as $item) {
                if ($item['type'] === self::TYPE_OBJECT) {
                    $item['value'] = unserialize($item['value']);
                }

                $this->_data[$item['key']] = $item['value'];
            }
        }

        return $this;
    }

    /**
     * Save data to file.
     *
     * @param array $data Data to save
     * 
     * @return \PF\Main\Cache\File
     */
    private function _saveToFile($data) {
        if (count($data) > 0) {
            if ($this->_file->isOpen() === false) {
                $this->_file->open(true);
            }

            $toSave = array();
            //TODO recursive iteration
            foreach ($data as $key => $value) {
                $item = array(
                    'key'  => $key,
                    'type' => self::TYPE_OTHER
                );

                if (is_object($value)) {
                    $value        = serialize($value);
                    $item['type'] = self::TYPE_OBJECT;
                }

                $item['value'] = $value;
                $toSave[] = $item;
            }

            $json = json_encode($toSave);

            $this->_file->fputs($json."\r\n");
        }

        return $this;
    }
}
