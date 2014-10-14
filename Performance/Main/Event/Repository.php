<?php

namespace PF\Main\Event;

use PF\Main\Abstracts;

/**
 * This script defines repository class for event repository.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
class Repository extends Abstracts\Repository {

    /**
     * Init method for set managed table.
     *
     * @return void
     */
    protected function init() {
        parent::init('event_log');
    }

    /**
     * Saving message from mediator.
     *
     * @param \PF\Main\Event\Interfaces\Message  $message  Message instance
     * @param \PF\Main\Event\Interfaces\Sender   $sender   Instance of sender
     * @param \PF\Main\Event\Interfaces\Reciever $reciever Instance of reciever
     *
     * @return int ID of message
     */
    public function saveMessage(Interfaces\Message $message, Interfaces\Sender $sender, Interfaces\Reciever $reciever) {
        $type          = Enum\Type::MESSAGE;
        $senderClass   = get_class($sender);
        $recieverClass = get_class($reciever);
        $messageClass  = get_class($message);

        return $this->_save($type, $messageClass, $senderClass, $recieverClass);
    }

    /**
     * Save event from event manager.
     *
     * @param \PF\Main\Event\Interfaces\Event    $event    Instance of event
     * @param \PF\Main\Event\Interfaces\Listener $listener Instance of listener
     *
     * @return int ID of message
     */
    public function saveEvent(Interfaces\Event $event, Interfaces\Listener $listener) {
        $type         = Enum\Type::EVENT;
        $eventName    = $event->getName();
        $module       = $event->getModule();
        $listenerName = $listener->getName();

        return $this->_save($type, $eventName, $module, $listenerName);
    }

    /**
     * Save message into database.
     *
     * @param enum   $type    One of \PF\Main\Event\Enum\Type
     * @param string $message Message of event
     * @param string $from    Source of message
     * @param string $to      Destination of message
     *
     * @return int ID of message
     */
    private function _save($type, $message, $from, $to) {
        $data = array(
            'type'    => $type,
            'message' => $message,
            'from'    => $from,
            'to'      => $to,
            'created' => $this->getUtils()->convertTimeToMySQLDateTime()
        );

        return parent::create($data);
    }
}
