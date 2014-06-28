<?php

namespace PF\Profiler\Monitor\Interfaces;

/**
 * Interface for monitor call.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
interface Call {

    /**
     * Returns call information from given backtrace, start and end times.
     *
     * @param array $backtrace Backtrace
     * @param int   $startTime Start time of call
     * @param int   $endTime   End time of call
     *
     * @return array Array with information about call
     */
    public function createCall($backtrace, $startTime, $endTime);

    /**
     * Returns content from file and line in call. It can be hash for decoding.
     *
     * @param array $call Array with information about call
     */
    public function getContent($call);

    /**
     * Returns flag that call is statement (one of if, for, while, foreach ...)
     *
     * @param array $call Array with information about call
     *
     * @return boolean
     */
    public function isStatement($call);

    /**
     * Returns content of line and file by hash. It is stored in cache.
     *
     * @param string $contentHash Hash of content
     *
     * @return string
     */
    public function decodeContentHash($contentHash);

    /**
     * Returns whole filename by hash. It is stored in cache.
     *
     * @param string $fileHash Hash of whole filename
     *
     * @return string
     */
    public function decodeFilenameHash($fileHash);
}
