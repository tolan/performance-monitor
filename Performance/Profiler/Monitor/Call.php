<?php

namespace PF\Profiler\Monitor;

/**
 * This script defines class for monitor call fly weight.
 * It has properties like as design pattern fly weight but it cache hashes and data for performance optimalization.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Call implements Interfaces\Call {

    const COMPLETE = 0;
    const FORWARD  = 1;
    const BACKWARD = 2;

    /**
     * Patterns for resolve statement.
     *
     * @var array
     */
    private $_statementPaterns = array(
        '[^a-z0-9]if|^if ?\(', '[^a-z0-9]else|^else ?\{', '[^a-z0-9]else|^else ?if ?\(', 'while ?\(', 'for ?\(', 'foreach ?\(', 'do ?\{'
    );

    /**
     * Compiled statement patterns. It is for performance optimalization.
     *
     * @var string
     */
    private $_statementRegExp = null;

    /**
     * Cache for loaded files.
     *
     * @var array
     */
    private static $_filesCache = array();

    /**
     * Cache for loaded files and lines and their hashed content.
     *
     * @var array
     */
    private $_linesCache = array();

    /**
     * Array for keep hashes between content hash and real content.
     *
     * @var array
     */
    private $_contentsHashes = array();

    /**
     * Array for keep hashes between file hashes and files.
     *
     * @var array
     */
    private $_fileHashes = array();

    /**
     * Array for keep hashes beween files and file hashes. It same as fileHashes but flipped. It is for performance optimalization.
     *
     * @var array
     */
    private $_fileHashesFlip = array();

    /**
     * Construct method.
     *
     * @return void
     */
    final public function __construct() {
        $this->_statementRegExp = '#('.join('|', $this->_statementPaterns).')#';
    }

    /**
     * Returns call information from given backtrace, start and end times.
     *
     * @param array $backtrace Backtrace
     * @param int   $startTime Start time of call
     * @param int   $endTime   End time of call
     *
     * @return array Array with information about call
     */
    public function createCall($backtrace, $startTime, $endTime) {
        $filename = $backtrace[0]['file'];
        if (isset($this->_fileHashes[$filename])) {
            $fileHash = $this->_fileHashes[$filename];
        } else {
            $fileHash                         = uniqid();
            $this->_fileHashes[$filename]     = $fileHash;
            $this->_fileHashesFlip[$fileHash] = $filename;
        }

        return array(
            Enum\CallAttributes::FILE       => $fileHash,
            Enum\CallAttributes::LINE       => $backtrace[0]['line'],
            Enum\CallAttributes::IMMERSION  => count($backtrace),
            Enum\CallAttributes::START_TIME => $startTime,
            Enum\CallAttributes::END_TIME   => $endTime
        );
    }

    /**
     * Returns content of line and file by hash. It is stored in cache.
     *
     * @param string $contentHash Hash of content
     *
     * @return string
     */
    public function decodeContentHash($contentHash) {
        return isset($this->_contentsHashes[$contentHash]) ? $this->_contentsHashes[$contentHash] : $contentHash;
    }

    /**
     * Returns whole filename by hash. It is stored in cache.
     *
     * @param string $fileHash Hash of whole filename
     *
     * @return string
     */
    public function decodeFilenameHash($fileHash) {
        return isset($this->_fileHashesFlip[$fileHash]) ? $this->_fileHashesFlip[$fileHash] : $fileHash;
    }

    /**
     * Returns flag that call is cycle (one of for, while, foreach ...)
     *
     * @param array $call Array with information about call
     *
     * @return boolean
     */
    public function isStatement($call) {
        $isCycle = false;

        if (!empty($call) && $this->_hasAttributes($call, array(Enum\CallAttributes::FILE, Enum\CallAttributes::LINE))) {
            if (!$this->_hasAttributes($call, Enum\CallAttributes::CONTENT)) {
                $call[Enum\CallAttributes::CONTENT] = $this->getContent($call)[Enum\CallAttributes::CONTENT];
            }

            $isCycle = preg_match($this->_statementRegExp, $this->_contentsHashes[$call[Enum\CallAttributes::CONTENT]]);
        }

        return $isCycle;
    }

    /**
     * Returns content hash of line in file.
     *
     * @param string $filename Path to file
     * @param int    $line     Line of file
     *
     * @return string Content hash of line in file
     */
    public function getContent($call) {
        if (!$this->_hasAttributes($call, array(Enum\CallAttributes::FILE, Enum\CallAttributes::LINE))) {
            throw new Exception('Input must be complete call from method createCall.');
        }

        $filename = $this->_fileHashesFlip[$call[Enum\CallAttributes::FILE]];
        $line     = $call[Enum\CallAttributes::LINE];

        if (!isset($this->_linesCache[$filename]) || !isset($this->_linesCache[$filename][$line])) {
            if (!isset(self::$_filesCache[$filename])) {
                if (file_exists($filename)) {
                    self::$_filesCache[$filename] = file($filename);
                } else {
                    self::$_filesCache[$filename] = array();
                }

                $this->_linesCache[$filename] = array();
            }

            $file        = self::$_filesCache[$filename];
            $pointerLine = $line;
            $maxLine     = count($file);

            $result       = trim($file[$pointerLine - 1]);
            $lineContent  = '';
            $countOfLines = 1;

            while(($completed = $this->_checkCompleteContent($lineContent.' '.$result)) !== self::COMPLETE && $pointerLine > 1 && $pointerLine <= $maxLine) {
                if ($completed === self::FORWARD) {
                    $pointerLine++;
                    $lineContent = $lineContent.' '.trim($file[$pointerLine - 1]);
                } else {
                    $pointerLine--;
                    $lineContent = trim($file[$pointerLine - 1]).' '.$lineContent;
                }

                $countOfLines++;
            }

            if ($countOfLines > 1) {
                $result = trim($file[$pointerLine - 1]).' ... '.$result;
            }

            $resultHash = uniqid();
            $this->_contentsHashes[$resultHash] = $result;
            $content = array(
                Enum\CallAttributes::CONTENT => $resultHash,
                Enum\CallAttributes::LINES   => $countOfLines
            );

            $this->_linesCache[$filename][$line] = $content;
        }

        return $this->_linesCache[$filename][$line];
    }

    /**
     * This checks whether content of line is complete.
     *
     * @param string $content Content of line
     *
     * @return boolean
     */
    private function _checkCompleteContent($content) {
        $content = trim($content);
        $result  = self::COMPLETE;
        $pairs   = array(
            array('{', '}'),
            array('(', ')'),
            array('[', ']'),
            array('"', '"'),
            array("'", "'")
        );

        foreach ($pairs as $pair) {
            if (substr_count($content, $pair[0]) > substr_count($content, $pair[1])) {
                $result = self::FORWARD;
                break;
            } elseif (substr_count($content, $pair[0]) < substr_count($content, $pair[1])) {
                $result = self::BACKWARD;
                break;
            }

            if (strpos($content, $pair[1]) === 0) {
                $result = self::BACKWARD;
                break;
            }
        }

        return $result;
    }

    /**
     * Returns wheter parameter call has all required attributes in second parameter.
     *
     * @param array $call       Array with information about call
     * @param arry  $attributes Array with required attribute names
     *
     * @return boolean
     */
    private function _hasAttributes($call, $attributes = array()) {
        $has = true;

        if (!is_array($call)) {
            $has = false;
        } else {
            foreach ((array)$attributes as $attribute) {
                if (!array_key_exists($attribute, $call)) {
                    $has = false;
                }
            }
        }

        return $has;
    }
}
