<?php

namespace PF\Main;

use PF\Main\Event\Interfaces\Reciever as EventReciever;
use PF\Main\Event\Interfaces\Message as EventMessage;
use PF\Main\Event\Interfaces\Sender as EventSender;
use PF\Main\Log\Enum\Level;
use PF\Main\Config;
use PF\Main\Event\Mediator;

/**
 * This script defines class for logging messages to file.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 *
 * @method \PF\Main\Log trace(mixed $message)   It provides logging into file with trace level.
 * @method \PF\Main\Log debug(mixed $message)   It provides logging into file with debug level.
 * @method \PF\Main\Log info(mixed $message)    It provides logging into file with info level.
 * @method \PF\Main\Log warning(mixed $message) It provides logging into file with warning level.
 * @method \PF\Main\Log error(mixed $message)   It provides logging into file with error level.
 * @method \PF\Main\Log fatal(mixed $message)   It provides logging into file with fatal level.
 */
class Log implements EventReciever {

    /**
     * Singleton instance of \PF\Main\Log.
     *
     * @var \PF\Main\Log
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
     * @var enum \PF\Main\Log\Enum\Level
     */
    private $_level = Level::OFF;

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
     * @param \PF\Main\Config         $config   Config instance
     * @param \PF\Main\Event\Mediator $mediator Mediator instance
     */
    private function __construct(Config $config = null, Mediator $mediator = null) {
        if ($mediator) {
            $mediator->register($this);
        }

        if ($config !== null) {
            $settings = $config->get('log');
            $this->_caching = isset($settings['cache']) ? $settings['cache'] : $this->_caching;
            $this->_level   = isset($settings['level']) ? $settings['level'] : $this->_level;
            $this->_file    = $this->_resolveFile($settings['file'], $config->get('root'));
        }
    }

    /**
     * It recieve message from mediator.
     *
     * @param \PF\Main\Event\Interface\Message $message Message instance
     * @param \PF\Main\Event\Interface\Sender  $sender  Sender instance
     *
     * @return \PF\Main\Log
     */
    public function recieve(EventMessage $message, EventSender $sender) {
        if ($this->_level <= Level::DEBUG) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);

            $messsage = new Log\Message(
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
     * @param \PF\Main\Config         $config   Config instance
     * @param \PF\Main\Event\Mediator $mediator Mediator instance
     *
     * @return \PF\Main\Log
     */
    public static function getInstance(Config $config = null, Mediator $mediator = null) {
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
     * @return \PF\Main\Log
     *
     * @throws \PF\Main\Log\Exception Throws when called method is not defined in level enum
     */
    public function __call($name, $arguments) {
        $level     = strtoupper($name);
        $constants = Level::getConstants();

        if (array_key_exists($level, $constants)) {
            if ($constants[$level] >= $this->_level && $this->_level !== Level::OFF) {
                $trace    = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
                $messsage = new Log\Message($level, $arguments, $trace[0]['file'], $trace[0]['line']);
                $this->_addMessage($messsage);
            }
        } else {
            throw new Log\Exception('Undefined log level: '.$level);
        }

        return $this;
    }

    /**
     * It resolves right target path of log file.
     *
     * @param string $path Traget path
     * @param string $root Root path of application
     *
     * @return string
     *
     * @throws \PF\Main\Log\Exception Throws when path is undefined
     */
    private function _resolveFile($path = null, $root = null) {
        if ($path === null) {
            throw new Log\Exception('Log path must be defined.');
        }
        if (strpos($path, '/') === 0) {
            return $path;
        }

        return $root.'/'.$path;
    }

    /**
     * This method handle message for caching or writing.
     *
     * @param \PF\Main\Log\Message $message Message instance
     *
     * @return \PF\Main\Log
     */
    private function _addMessage(Log\Message $message) {
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
     * @param \PF\Main\Log\Message $message Message instnace
     *
     * @return \PF\Main\Log
     *
     * @throws \PF\Main\Log\Exception Throws when is not set filename for log.
     */
    private function _writeMessage(Log\Message $message) {
        if ($this->_file === null) {
            throw new Log\Exception('Filename for logging is not defined.');
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
