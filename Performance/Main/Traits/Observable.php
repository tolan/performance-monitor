<?php

namespace PF\Main\Traits;

use PF\Main\Interfaces\Observer;

trait Observable {

    private $_observers = array();

    public function attach (Observer $observer) {
        $this->_observers[] = $observer;

        return $this;
    }

    public function detach (Observer $observer) {
        $this->_observers = array_diff($this->_observers, array($observer));

        return $this;
    }

    public function notify () {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }

        return $this;
    }
}
