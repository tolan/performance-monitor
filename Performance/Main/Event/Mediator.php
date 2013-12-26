<?php

/**
 * This script defines class for main mediator.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Performance_Main_Event_Mediator
implements Performance_Main_Event_Interface_Mediator, Performance_Main_Event_Interface_Reciever {

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
     * Construct method. Register default recivevers.
     *
     * @param Performance_Main_Provider $provider
     */
    public function __construct(Performance_Main_Provider $provider) {
        foreach ($this->_initRecievers as $recieverClass) {
            $this->register($provider->get($recieverClass));
        }
    }

    /**
     * Register reciever for send message. Message is sent to the recipient by message type that receives its parameter (include children in object model).
     *
     * @param Performance_Main_Event_Interface_Reciever $reciever Reciever instance
     *
     * @return Performance_Main_Event_Mediator
     */
    public function register(Performance_Main_Event_Interface_Reciever $reciever) {
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
     * @param Performance_Main_Event_Interface_Reciever $reciever Reciever instance
     *
     * @return Performance_Main_Event_Mediator
     */
    public function unregister(Performance_Main_Event_Interface_Reciever $reciever) {
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
     * @param Performance_Main_Event_Interface_Message $message Message instance
     * @param Performance_Main_Event_Interface_Sender  $sender  Sender instance
     *
     * @return Performance_Main_Event_Mediator
     */
    public function send(Performance_Main_Event_Interface_Message $message, Performance_Main_Event_Interface_Sender $sender) {
        foreach ($this->_recievers as $messageClass => $recievers) {
            if (is_a($message, $messageClass, false)) {
                foreach ($recievers as $reciever) {
                    if ($reciever !== $sender) {
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
     * @param Performance_Main_Event_Interface_Message $message Message instance
     * @param Performance_Main_Event_Interface_Sender  $sender  Sender instance
     *
     * @return Performance_Main_Event_Mediator
     */
    public function recieve(Performance_Main_Event_Interface_Message $message, Performance_Main_Event_Interface_Sender $sender) {
        $this->send($message, $sender);

        return $this;
    }

    /**
     * Returns exact class name of message which reciever has as parameter in recieve method.
     *
     * @param Performance_Main_Event_Interface_Reciever $reciever Reciever instance
     *
     * @return string Message class name in parameter method recieve
     */
    private function _getMessageClass(Performance_Main_Event_Interface_Reciever $reciever) {
        $method       = new ReflectionMethod($reciever, 'recieve');
        $params       = $method->getParameters();
        $messageClass = $params[0]->getClass()->getName();

        return $messageClass;
    }
}
