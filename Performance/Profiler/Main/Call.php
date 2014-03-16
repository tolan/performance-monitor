<?php

namespace PF\Profiler\Main;

class Call implements Interfaces\Call {

    /**
     * Patterns for resolve cycles.
     *
     * @var array
     */
    private $_cyclesPaterns = array(
        'while ?\(', 'for ?\(', 'foreach ?\(', 'do ?\{'
    );

    private $_cycleRegExp = null;

    private static $_filesCache = array();

    private static $_linesCache     = array();
    private static $_contentsHashes = array();

    private static $_fileHashes     = array();
    private static $_fileHashesFlip = array();

    final public function __construct() {
        $this->_cycleRegExp = '#('.join('|', $this->_cyclesPaterns).')#';
    }

    public function createCall($backtrace, $startTime, $endTime) {
        $filename = $backtrace[0]['file'];
        if (isset(self::$_fileHashes[$filename])) {
            $fileHash = self::$_fileHashes[$filename];
        } else {
            $fileHash = uniqid();
            self::$_fileHashes[$filename]     = $fileHash;
            self::$_fileHashesFlip[$fileHash] = $filename;
        }

        return array(
            Enum\CallAttributes::FILE       => $fileHash,
            Enum\CallAttributes::LINE       => $backtrace[0]['line'],
            Enum\CallAttributes::IMMERSION  => count($backtrace),
            Enum\CallAttributes::START_TIME => $startTime,
            Enum\CallAttributes::END_TIME   => $endTime
        );
    }

    public function decodeContentHash($contentHash) {
        return isset(self::$_contentsHashes[$contentHash]) ? self::$_contentsHashes[$contentHash] : $contentHash;
    }

    public function decodeFilenameHash($fileHash) {
        return isset(self::$_fileHashesFlip[$fileHash]) ? self::$_fileHashesFlip[$fileHash] : $fileHash;
    }

    public function isCycle($call) {
        $isCycle = false;

        if (!empty($call) && $this->_hasAttributes($call, array(Enum\CallAttributes::FILE, Enum\CallAttributes::LINE))) {
            if (!$this->_hasAttributes($call, Enum\CallAttributes::CONTENT)) {
                $call[Enum\CallAttributes::CONTENT] = $this->getContent($call)[Enum\CallAttributes::CONTENT];
            }

            $isCycle = preg_match($this->_cycleRegExp, self::$_contentsHashes[$call[Enum\CallAttributes::CONTENT]]);
        }

        return $isCycle;
    }

    /**
     * Returns content of line in file.
     *
     * @param string $filename Path to file
     * @param int    $line     Line of file
     *
     * @return string Content of line in file
     */
    public function getContent($call) {
        if (!$this->_hasAttributes($call, array(Enum\CallAttributes::FILE, Enum\CallAttributes::LINE))) {
            throw new Exception('Input must be complete call from method createCall.');
        }

        $filename = self::$_fileHashesFlip[$call[Enum\CallAttributes::FILE]];
        $line     = $call[Enum\CallAttributes::LINE];

        if (!isset(self::$_linesCache[$filename]) || !isset(self::$_linesCache[$filename][$line])) {
            if (!isset(self::$_filesCache[$filename])) {
                self::$_filesCache[$filename] = file($filename);
                self::$_linesCache[$filename] = array();
            }

            $file        = self::$_filesCache[$filename];
            $pointerLine = $line;

            $result       = trim($file[$pointerLine - 1]);
            $countOfLines = 1;

            while(!$this->_checkCompleteContent($result) && $pointerLine > 1) {
                $pointerLine--;
                $countOfLines++;
                $result = trim($file[$pointerLine - 1]).' '.$result;
            }

            $resultHash = uniqid();
            self::$_contentsHashes[$resultHash] = $result;
            $content = array(
                Enum\CallAttributes::CONTENT => $resultHash,
                Enum\CallAttributes::LINES   => $countOfLines
            );

            self::$_linesCache[$filename][$line] = $content;
        }

        return self::$_linesCache[$filename][$line];
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
        $result  = true;
        $pairs   = array(
            array('{', '}'),
            array('(', ')'),
            array('[', ']'),
            array('"', '"'),
            array("'", "'")
        );

        foreach ($pairs as $pair) {
            if (substr_count($content, $pair[0]) !== substr_count($content, $pair[1])) {
                $result = false;
                break;
            }

            if (strpos($content, $pair[1]) === 0) {
                $result = false;
                break;
            }
        }

        return $result;
    }

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
