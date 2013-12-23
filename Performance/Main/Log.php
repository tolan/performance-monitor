<?php

/**
 * This script defines class for logging messages to file.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method Performance_Main_Log trace(mixed $message) It provides logging into file with trace level.
 * @method Performance_Main_Log debug(mixed $message) It provides logging into file with debug level.
 * @method Performance_Main_Log info(mixed $message) It provides logging into file with info level.
 * @method Performance_Main_Log warning(mixed $message) It provides logging into file with warning level.
 * @method Performance_Main_Log error(mixed $message) It provides logging into file with error level.
 * @method Performance_Main_Log fatal(mixed $message) It provides logging into file with fatal level.
 */
class Performance_Main_Log implements Performance_Main_Event_Interface_Reciever {
    /**
     * Singleton instance of Performance_Main_Log.
     *
     * @var Performance_Main_Log
     */
    private static $_instance = false;

    /**
     * Flag for caching messages for improve performance and delaying write to file.
     *
     * @var boolean
     */
    private $_caching = false;

    /**
     * Log level.
     *
     * @var enum Performance_Main_Log_Enum_Level
     */
    private $_level   = Performance_Main_Log_Enum_Level::OFF;

    /**
     * Log filename
     *
     * @var string
     */
    private $_file    = null;

    /**
     * Storage for messages
     *
     * @var array
     */
    private $_cacheLogs = array();

    /**
     * Construct method.
     *
     * @param Performance_Main_Config         $config   Config instance
     * @param Performance_Main_Event_Mediator $mediator Mediator instance
     */
    private function __construct(Performance_Main_Config $config = null, Performance_Main_Event_Mediator $mediator = null) {
        if ($mediator) {
            $mediator->register($this);
        }

        if ($config !== null) {
            $settings = $config->get('log');
            $this->_caching = isset($settings['cache']) ? $settings['cache'] : $this->_caching;
            $this->_level   = isset($settings['level']) ? $settings['level'] : $this->_level;
            $this->_file    = isset($settings['file'])  ? $settings['file']  : $this->_file;
        }
    }

    /**
     * It recieve message from mediator.
     *
     * @param Performance_Main_Event_Interface_Message $message Message instance
     * @param Performance_Main_Event_Interface_Sender  $sender  Sender instance
     *
     * @return Performance_Main_Log
     */
    public function recieve(Performance_Main_Event_Interface_Message $message, Performance_Main_Event_Interface_Sender $sender) {
        if ($this->_level <= Performance_Main_Log_Enum_Level::DEBUG) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            $messsage = new Performance_Main_Log_Message(
                'MESSAGE',
                array(get_class($sender), $message->getData()),
                $trace[1]['file'],
                $trace[1]['line']
            );

            $this->_addMessage($messsage);
        }

        return $this;
    }

    /**
     * Returns singleton instance.
     *
     * @param Performance_Main_Config         $config   Config instance
     * @param Performance_Main_Event_Mediator $mediator Mediator instance
     *
     * @return Performance_Main_Log
     */
    public static function getInstance(Performance_Main_Config $config = null, Performance_Main_Event_Mediator $mediator = null) {
        if (self::$_instance === false) {
            self::$_instance = new self($config, $mediator);
        }

        return self::$_instance;
    }

    /**
     * Magic method for call logging method.
     *
     * @param string $name      Logging level
     * @param array  $arguments Arguments of log
     *
     * @return Performance_Main_Log
     *
     * @throws Performance_Main_Log_Exception Throws when called method is not defined in level enum
     */
    public function __call($name, $arguments) {
        $level     = strtoupper($name);
        $constants = Performance_Main_Log_Enum_Level::getConstants();

        if (array_key_exists($level, $constants)) {
            if ($constants[$level] <= $this->_level && $this->_level !== Performance_Main_Log_Enum_Level::OFF) {
                $trace    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                $messsage = new Performance_Main_Log_Message($level, $arguments, $trace[0]['file'], $trace[0]['line']);
                $this->_addMessage($messsage);
            }
        } else {
            throw new Performance_Main_Log_Exception('Undefined log level: '.$level);
        }

        return $this;
    }

    /**
     * This method handle message for caching or writing.
     *
     * @param Performance_Main_Log_Message $message Message instance
     *
     * @return Performance_Main_Log
     */
    private function _addMessage(Performance_Main_Log_Message $message) {
        if ($this->_caching === true) {
            $this->_cacheLogs[] = $message;
        } else {
            $this->_writeMessage($message);
        }

        return $this;
    }

    /**
     * Write message. This ensure right setting for message.
     *
     * @param Performance_Main_Log_Message $message Message instnace
     *
     * @return Performance_Main_Log
     *
     * @throws Performance_Main_Log_Exception Throws when is not set filename for log.
     */
    private function _writeMessage(Performance_Main_Log_Message $message) {
        if ($this->_file === null) {
            throw new Performance_Main_Log_Exception('Filename for logging is not defined.');
        }

        $message->write($this->_file);

        return $this;
    }

    /**
     * Destruct method which write all cached messages.
     *
     * @return void
     */
    public function __destruct() {
        foreach ($this->_cacheLogs as $message) {
            $this->_writeMessage($message);
        }
    }
}
