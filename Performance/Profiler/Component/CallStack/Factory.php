<?php

namespace PF\Profiler\Component\CallStack;

use PF\Profiler\Component\AbstractFactory;

/**
 * This script defines factory class for creating call stack by request settings.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Factory extends AbstractFactory {

    /**
     * Returns call stack instance.
     *
     * @return \PF\Profiler\Component\CallStack\AbstractCallStack
     */
    public function getCallStack() {
        if ($this->getAttemptId()) {
            $callStack = $this->getProvider()->get('PF\Profiler\Component\CallStack\MySQL');
            $callStack->setAttemptId($this->getAttemptId());
        } else {
            $callStack = $this->getProvider()->get('PF\Profiler\Component\CallStack\Browser');
        }

        return $callStack;
    }
}
