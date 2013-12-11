<?php

/**
 * This script defines class for log message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Log_Message {
    /**
     * Date format for logging.
     *
     * @var string
     */
    private static $_dateFormat = 'Y-m-d H:i:s';

    /**
     * Log level.
     *
     * @var enum Performance_Main_Log_Enum_Level
     */
    private $_level     = Performance_Main_Log_Enum_Level::OFF;

    /**
     * File where is log called.
     *
     * @var string
     */
    private $_file      = null;

    /**
     * Line where is log called.
     *
     * @var int
     */
    private $_line      = null;

    /**
     * Arguments of message.
     *
     * @var array
     */
    private $_arguments = array();

    /**
     * Construct method.
     *
     * @param enum  $level     One of Performance_Main_Log_Enum_Level
     * @param array $arguments Arguments of message
     */
    public function __construct($level, $arguments) {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

        $this->_level     = $level;
        $this->_file      = $trace[1]['file'];
        $this->_line      = $trace[1]['line'];
        $this->_arguments = $arguments;
    }

    /**
     * Write message to file.
     *
     * @param string $file Path to the file
     */
    public function write($file) {
        if (!file_exists($file)) {
            touch($file);
        }

        file_put_contents($file, $this->_compileMessage(), FILE_APPEND);
    }

    /**
     * Returns compiled message.
     *
     * @return string
     */
    private function _compileMessage() {
        $text = date(self::$_dateFormat)."# ".$this->_level."\r\n";
        $text .= $this->_file.':'.$this->_line."#\r\n";
        foreach ($this->_arguments as $argument) {
            $text .= var_export($argument, TRUE)."\r\n";
        }

        return $text;
    }
}
