<?php

namespace PF\Profiler\Main\Factory;

class Display extends AbstractFactory {

    public function getDisplay() {
        $display = null;

        switch ($this->_getType()) {
            case self::TYPE_MYSQL:
                $display = $this->getProvider()->get('PF\Profiler\Main\Display\MySQL');
                break;
            case self::TYPE_CACHE:
                $display = $this->getProvider()->get('PF\Profiler\Main\Display\Cache');
                break;
            default:
                throw new Exception('Display doesn\'t exist for '.$this->_getType().'.');
        }

        return $display;
    }

    private function _getType() {
        // TODO implement get type by request params
        return self::TYPE_CACHE;
    }
}
