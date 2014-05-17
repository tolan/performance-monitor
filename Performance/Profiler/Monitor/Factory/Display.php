<?php

namespace PF\Profiler\Monitor\Factory;

use PF\Profiler\Monitor\Enum\Type;

/**
 * This script defines factory class for monitor display.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Display extends AbstractFactory {

    /**
     * Returns instance of monitor display.
     *
     * @return \PF\Profiler\Monitor\Display\AbstractDisplay
     *
     * @throws Exception Throws when type is not defined.
     */
    public function getDisplay() {
        $display = null;

        switch ($this->getType()) {
            case Type::MYSQL:
                $display = $this->getProvider()->get('PF\Profiler\Monitor\Display\MySQL');
                break;
            case Type::SESSION:
                $display = $this->getProvider()->get('PF\Profiler\Monitor\Display\Session');
                break;
            case Type::FILE:
                $display = $this->getProvider()->get('PF\Profiler\Monitor\Display\Cache');
                break;
            default:
                throw new Exception('Display doesn\'t exist for '.$this->getType().'.');
        }

        return $display;
    }
}
