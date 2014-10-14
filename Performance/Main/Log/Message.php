<?php

namespace PF\Main\Log;

/**
 * This script defines class for log message.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Message {

    /**
     * Date format for logging.
     *
     * @var string
     */
    private static $_dateFormat = 'Y-m-d H:i:s';

    /**
     * Log level.
     *
     * @var enum \PF\Main\Log\Enum\Level
     */
    private $_level = Enum\Level::OFF;

    /**
     * File where is log called.
     *
     * @var string
     */
    private $_file = null;

    /**
     * Line where is log called.
     *
     * @var int
     */
    private $_line = null;

    /**
     * Arguments of message.
     *
     * @var array
     */
    private $_arguments = array();

    /**
     * Construct method.
     *
     * @param enum  $level     One of \PF\Main\Log\Enum\Level
     * @param array $arguments Arguments of message
     * @param file  $file      File where is log called
     * @param int   $line      Line where is log called
     */
    public function __construct($level, $arguments, $file, $line) {
        $this->_level     = $level;
        $this->_file      = $file;
        $this->_line      = $line;
        $this->_arguments = $arguments;
    }

    /**
     * Write message to file.
     *
     * @param string $file Path to the file
     *
     * @return \PF\Main\Log\Message
     */
    public function write($file) {
        if (!file_exists($file)) {
            touch($file);
        }

        file_put_contents($file, $this->_compileMessage(), FILE_APPEND);

        return $this;
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
            if ($argument instanceof \Exception) {
                $argument = array(
                    'message' => $argument->getMessage(),
                    'trace'   => $argument->getTraceAsString()
                );
            }

            $text .= var_export($argument, TRUE)."\r\n";
        }

        return $text;
    }
}
