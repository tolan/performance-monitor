<?php

namespace PM\Main\Filesystem;

/**
 * This script defines class for manage file in filesystem.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class File {

    /**
     * File pointer from fopen function.
     *
     * @var resource
     */
    private $_filePointer = null;

    /**
     * Dirname of file.
     *
     * @var string
     */
    private $_path = null;

    /**
     * Filename of file.
     *
     * @var string
     */
    private $_name = null;

    /**
     * Offset of read method.
     *
     * @var int
     */
    private $_readOffset = 0;

    /**
     * Offset of write method.
     *
     * @var int
     */
    private $_writeOffset = 0;

    /**
     * Flag for synchronization offsets.
     *
     * @var boolean
     */
    private $_syncOffsets = false;

    /**
     * Construct method.
     *
     * @param string  $path        Dirname of file
     * @param string  $name        Filename of file
     * @param boolean $open        Flag for automatic open file
     * @param boolean $autoCreate  Flag for automatic creation of file when it doesn't exist
     * @param booelan $syncOffsets Flag for synchronization offsets
     *
     * @return void
     */
    public function __construct($path, $name, $open = true, $autoCreate = false, $syncOffsets = false) {
        $this->_path        = $path;
        $this->_name        = $name;
        $this->_syncOffsets = $syncOffsets;

        if ($open === true) {
            $this->open($autoCreate);
        } elseif ($autoCreate === true) {
            $this->_createFile();
        }
    }

    /**
     * It provides copy this file to new path and create new instance of the copy.
     *
     * @param string $path Dirname of new file
     *
     * @return \PM\Main\Filesystem\File
     */
    public function copy($path) {
        copy($this->_getFilepath(), $path.'/'.$this->getName());
        $copy = new self($path, $this->_name, false, false, $this->_syncOffsets);

        return $copy;
    }

    /**
     * Returns filename of file.
     *
     * @return string
     */
    public function getName() {
        return $this->_name;
    }

    /**
     * Returns dirname of file.
     *
     * @return string
     */
    public function getPath() {
        return $this->_path;;
    }

    /**
     * Returns size of file in bytes.
     *
     * @return int
     */
    public function getFilesize() {
        return filesize($this->_getFilepath());
    }

    /**
     * Returns last modification date.
     *
     * @return int
     */
    public function getModificationDate() {
        return filemtime($this->_getFilepath());
    }

    /**
     * Returns flag that file is open.
     *
     * @return boolean
     */
    public function isOpen() {
        return $this->_filePointer !== null;
    }

    /**
     * Open file and sets pointer to default values.
     *
     * @param boolean $autoCreate Flag for automatic create file when doesn't exist
     *
     * @return \PM\Main\Filesystem\File
     */
    public function open($autoCreate = false) {
        if ($autoCreate === false) {
            $this->_checkFileExist();
        }

        if ($this->isOpen()) {
            $this->close();
        }

        $this->_createFile();
        $this->_filePointer = fopen($this->_getFilepath(), 'a+');
        $this->_readOffset  = 0;
        $this->_writeOffset = ftell($this->_filePointer);
        $this->_synchronizeOffsets($this->_writeOffset);

        return $this;
    }

    /**
     * Close file pointer.
     *
     * @return \PM\Main\Filesystem\File
     */
    public function close() {
        $this->_checkOpen();

        fclose($this->_filePointer);
        $this->_filePointer = null;

        return $this;
    }

    /**
     * Write data to file. It is alias for write method.
     *
     * @param string $data Data to save
     *
     * @return \PM\Main\Filesystem\File
     */
    public function fputs($data) {
        $this->write($data);

        return $this;
    }

    /**
     * Write data to file.
     *
     * @param string $data Data to save
     *
     * @return \PM\Main\Filesystem\File
     */
    public function write($data) {
        $this->_checkOpen();

        fseek($this->_filePointer, $this->_writeOffset);
        fwrite($this->_filePointer, $data);
        $this->_writeOffset = ftell($this->_filePointer);
        $this->_synchronizeOffsets($this->_writeOffset);

        return $this;
    }

    /**
     * Returns data from actual pointer to end line
     *
     * @return string
     */
    public function fgets() {
        $this->_checkOpen();

        fseek($this->_filePointer, $this->_readOffset);
        $data = fgets($this->_filePointer);
        $this->_readOffset = ftell($this->_filePointer);
        $this->_synchronizeOffsets($this->_readOffset);


        return $data;
    }

    /**
     * Reads entire file into an array
     *
     * @return array the file in an array.
     */
    public function file() {
        $this->_checkFileExist();
        $data = array();

        while(($line = fgets($this->_filePointer))) {
            $data[] = $line;
        }

        return $data;
    }

    /**
     * It provides touch method for file.
     *
     * @param boolean $autoCreate Flag for automatic create file when doesn't exist
     *
     * @return \PM\Main\Filesystem\File
     */
    public function touch($autoCreate = true) {
        if ($autoCreate === false) {
            $this->_checkFileExist();
        }

        touch($this->_getFilepath());

        return $this;
    }

    /**
     * It provides delete method for file. It close pointer and unlink file from filesystem.
     *
     * @return \PM\Main\Filesystem\File
     */
    public function delete() {
        if ($this->isOpen() === true) {
            $this->close();
        }

        umask(0000);
        unlink($this->_getFilepath());
        $this->setReadOffset(0);
        $this->setWriteOffset(0);

        return $this;
    }

    /**
     * Gets read offset.
     *
     * @return int
     */
    public function getReadOffset() {
        return $this->_readOffset;
    }

    /**
     * Sets read offset.
     *
     * @param int $offset Offset of read pointer
     *
     * @return \PM\Main\Filesystem\File
     */
    public function setReadOffset($offset) {
        $this->_readOffset = $offset;
        $this->_synchronizeOffsets($offset);

        return $this;
    }

    /**
     * Gets write offset.
     *
     * @return int
     */
    public function getWriteOffset() {
        return $this->_writeOffset;
    }

    /**
     * Sets write offset.
     *
     * @param int $offset Offset of write pointer
     *
     * @return \PM\Main\Filesystem\File
     */
    public function setWriteOffset($offset) {
        $this->_writeOffset = $offset;
        $this->_synchronizeOffsets($offset);

        return $this;
    }

    /**
     * Returns that file exists in filesystem.
     *
     * @return boolean
     */
    public function fileExist() {
        return $this->_isFileExist();
    }

    /**
     * Destruct method. It close file pointer.
     *
     * @return void
     */
    public function __destruct() {
        if ($this->_filePointer !== null && $this->isOpen() === true) {
            $this->close();
        }
    }

    /**
     * It creates file when doesn't exist.
     *
     * @return \PM\Main\Filesystem\File
     */
    private function _createFile() {
        if ($this->_isFileExist() === false) {
            touch($this->_getFilepath());
            $this->_checkFileExist();
        }

        return $this;
    }

    /**
     * Returns whole path of file include dirname and filename.
     *
     * @return string
     *
     * @throws \PM\Main\Filesystem\Exception Throws when path or name is not set.
     */
    private function _getFilepath() {
        if ($this->_path === null || $this->_name === null) {
            throw new Exception('Path or name is not set.');
        }

        return $this->_path.'/'.$this->_name;
    }

    /**
     * Method for synchronize offsets when synchronization is enabled.
     *
     * @param int $pointer New pointer value for synchronize
     *
     * @return \PM\Main\Filesystem\File
     */
    private function _synchronizeOffsets($pointer) {
        if ($this->_syncOffsets === true) {
            $this->_readOffset  = $pointer;
            $this->_writeOffset = $pointer;
        }

        return $this;
    }

    /**
     * Checks that file exists.
     *
     * @return \PM\Main\Filesystem\File
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file doesn't exist.
     */
    private function _checkFileExist() {
        if ($this->_isFileExist() === false) {
            throw new Exception('File doesn\'t exist.');
        }

        return $this;
    }

    /**
     * Returns flag that file exists.
     *
     * @return boolean
     */
    private function _isFileExist() {
        $filepath = $this->_getFilepath();
        return file_exists($filepath);
    }

    /**
     * Checks that file is open.
     *
     * @return \PM\Main\Filesystem\File
     *
     * @throws \PM\Main\Filesystem\Exception Throws when file is not open.
     */
    private function _checkOpen() {
        if ($this->isOpen() === false) {
            throw new Exception('File is not open.');
        }

        return $this;
    }
}
