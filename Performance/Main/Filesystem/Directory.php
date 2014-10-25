<?php

namespace PM\Main\Filesystem;

/**
 * This script defines class for manage directory in filesystem.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Directory {

    /**
     * Path to the directory.
     *
     * @var string
     */
    private $_path = null;

    /**
     * List of files in directory
     *
     * @var array Array of File[]
     */
    private $_files = array();

    /**
     * Construct method.
     *
     * @param string $path Path to the directory
     *
     * @return void
     */
    public function __construct($path) {
        $this->_path = $path;
        $this->_checkDir();
    }

    /**
     * Returns elements in directory by pattern and glob flags.
     *
     * @param string $pattern The pattern for search
     * @param type   $flags   Flags of glob function
     *
     * @return array
     */
    public function glob($pattern, $flags = 0) {
        $this->_checkDir();

        $ans = (glob($this->_path.'/'.$pattern, $flags));

        if ($ans === false) {
            $ans = array();
        }

        return $ans;
    }

    /**
     * Returns all elements in directory. Files are returned as instance of File and directories are as Directory.
     *
     * @return array
     */
    public function getAll() {
        $elements = array();
        foreach ((array)$this->glob('*') as $filepath) {
            if (is_file($filepath)) {
                $elements[] = $this->getFile(basename($filepath));
            } elseif (is_dir($filepath)) {
                $elements[] = new self($filepath);
            } else {
                $elements[] = $filepath;
            }
        }

        return $elements;
    }

    /**
     * Create new file in directory by filename.
     *
     * @param string $name Name of file
     *
     * @return \PM\Main\Filesystem\Directory
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file already exists.
     */
    public function createFile($name) {
        $this->_checkDir();
        if ($this->fileExists($name)) {
            throw new Exception('You can not create a new file already exists.');
        }

        $this->_createFileToCache($name);

        return $this;
    }

    /**
     * Returns instance of File by given filename.
     *
     * @param string $name Name of file
     *
     * @return \PM\Main\Filesystem\File
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file doesn't exist.
     */
    public function getFile($name) {
        if ($this->fileExists($name) == false) {
            throw new Exception('The file "'.$name.'" doesn\'t exist.');
        }

        if ($this->_fileInCache($name) === false) {
            $this->_createFileToCache($name);
        }

        return $this->_files[$name];
    }

    /**
     * Delete file in directory by given filename.
     *
     * @param string $name Name of file
     *
     * @return \PM\Main\Filesystem\Directory
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file doesn't exist.
     */
    public function deleteFile($name) {
        if ($this->fileExists($name) === false) {
            throw new Exception('The file "'.$name.'" doesn\'t exist.');
        }

        $this->getFile($name)->delete();
        unset($this->_files[$name]);

        return $this;
    }

    /**
     * This method copy file into this directory from different directory.
     *
     * @param \PM\Main\Filesystem\File $file File instance from different directory
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file already exists or copy fails.
     */
    public function copyTo(File $file) {
        if ($file->getPath() === $this->_path || $this->fileExists($file->getName()) === true) {
            throw new Exception('You can not copy file already exists');
        }

        $copy = $file->copy($this->_path);
        if ($copy->fileExists($file->getName()) === false) {
            throw new Exception('Copy of the file failed.');
        }

        $this->_files[$copy->getName()] = $copy;
    }

    /**
     * Returns that file exists.
     *
     * @param string $name Name of file
     *
     * @return boolean
     */
    public function fileExists($name) {
        $this->_checkDir();

        return file_exists($this->_path.'/'.$name);
    }

    /**
     * It create new File and save it into cache.
     *
     * @param string $name Name of file
     *
     * @return \PM\Main\Filesystem\Directory
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file already exists.
     */
    private function _createFileToCache($name) {
        if ($this->_fileInCache($name) === true) {
            throw new Exception('The file already exists.');
        }

        $this->_files[$name] = new File($this->_path, $name, false, true, false);

        return $this;
    }

    /**
     * Returns that file is in cache.
     *
     * @param string $name Name of file
     *
     * @return boolean
     */
    private function _fileInCache($name) {
        return array_key_exists($name, $this->_files);
    }

    /**
     * Checks that dir exists.
     *
     * @return \PM\Main\Filesystem\Directory
     *
     * @throws \PM\Main\Filesystem\Exception Throws when given path is not a directory.
     */
    private function _checkDir() {
        if(!is_dir($this->_path)) {
            throw new Exception('Given path "'.$this->_path.'" is not a directory.');
        }

        return $this;
    }
}
