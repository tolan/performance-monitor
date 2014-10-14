<?php

namespace PF\Main\Event;

use PF\Main\Provider;

/**
 * This script defines class for main mediator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Mediator implements Interfaces\Mediator, Interfaces\Reciever {

    /**
     * List of recievers which are registred when is mediator created.
     *
     * @var array
     */
    protected $_initRecievers = array();

    /**
     * Stack of recievers in structure array('messageClass' => array(recievers)).
     *
     * @var array
     */
    private $_recievers = array();

    /**
     * Repository instance.
     *
     * @var \PF\Main\Event\Repository
     */
    private $_repository = null;

    /**
     * Construct method. Register default recivevers.
     *
     * @param \PF\Main\Provider         $provider   Provider instance
     * @param \PF\Main\Event\Repository $repository Repository instance
     *
     * @return void
     */
    public function __construct(Provider $provider, Repository $repository) {
        $this->_repository = $repository;

        foreach ($this->_initRecievers as $recieverClass) {
            $this->register($provider->get($recieverClass));
        }
    }

    /**
     * Register reciever for send message. Message is sent to the recipient by message type that receives its parameter (include children in object model).
     *
     * @param \PF\Main\Event\Interfaces\Reciever $reciever Reciever instance
     *
     * @return \PF\Main\Event\Mediator
     */
    public function register(Interfaces\Reciever $reciever) {
        $messageClass = $this->_getMessageClass($reciever);

        if (isset($this->_recievers[$messageClass])) {
            foreach ($this->_recievers[$messageClass] as $registred) {
                if ($registred === $reciever) {
                    return $this;
                }
            }
        }

        $this->_recievers[$messageClass][] = $reciever;

        return $this;
    }

    /**
     * This method provides the deregistration of the mediator.
     *
     * @param \PF\Main\Event\Interfaces\Reciever $reciever Reciever instance
     *
     * @return \PF\Main\Event\Mediator
     */
    public function unregister(Interfaces\Reciever $reciever) {
        $messageClass = $this->_getMessageClass($reciever);

        if (isset($this->_recievers[$messageClass])) {
            foreach ($this->_recievers[$messageClass] as $key => $registred) {
                if ($registred === $reciever) {
                    array_splice($this->_recievers[$messageClass], $key, 1);

                    return $this;
                }
            }
        }

        return $this;
    }

    /**
     * This method sends message to all recievers which has concrete message class in its parameter.
     *
     * @param \PF\Main\Event\Interface\Message $message Message instance
     * @param \PF\Main\Event\Interface\Sender  $sender  Sender instance
     *
     * @return \PF\Main\Event\Mediator
     */
    public function send(Interfaces\Message $message, Interfaces\Sender $sender) {
        foreach ($this->_recievers as $messageClass => $recievers) {
            if (is_a($message, $messageClass, false)) {
                foreach ($recievers as $reciever) {
                    if ($reciever !== $sender) {
                        $this->_repository->saveMessage($message, $sender, $reciever);
                        $reciever->recieve($message, $sender);
                    }
                }
            }
        }

        return $this;
    }

    /**
     * This is proxy method for send method.
     *
     * @param \PF\Main\Event\Interface\Message $message Message instance
     * @param \PF\Main\Event\Interface\Sender  $sender  Sender instance
     *
     * @return \PF\Main\Event\Mediator
     */
    public function recieve(Interfaces\Message $message, Interfaces\Sender $sender) {
        $this->send($message, $sender);

        return $this;
    }

    /**
     * Returns exact class name of message which reciever has as parameter in recieve method.
     *
     * @param \PF\Main\Event\Interface\Reciever $reciever Reciever instance
     *
     * @return string Message class name in parameter method recieve
     */
    private function _getMessageClass(Interfaces\Reciever $reciever) {
        $method       = new \ReflectionMethod($reciever, 'recieve');
        $params       = $method->getParameters();
        $messageClass = $params[0]->getClass()->getName();

        return $messageClass;
    }
}
