<?php

namespace PF\Main\Traits;

use PF\Main\Interfaces\Observer;

/**
 * This script defines trait for observable objects.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Main
 */
trait Observable {

    /**
     * List of observers.
     *
     * @var array
     */
    private $_observers = array();

    /**
     * Attach observer to list of observers.
     *
     * @param \PF\Main\Interfaces\Observer $observer Observer instance
     */
    public function attach (Observer $observer) {
        $this->_observers[] = $observer;

        return $this;
    }

    /**
     * Detach observer from list of observers.
     *
     * @param \PF\Main\Interfaces\Observer $observer Observer instance
     */
    public function detach (Observer $observer) {
        $this->_observers = array_diff($this->_observers, array($observer));

        return $this;
    }

    /**
     * Notify all observers.
     */
    public function notify () {
        foreach ($this->_observers as $observer) {
            $observer->updateObserver($this);
        }

        return $this;
    }
}
