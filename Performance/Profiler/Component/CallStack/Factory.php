<?php

/**
 * This script defines factory class for creating call stack by request settings.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_CallStack_Factory extends Performance_Profiler_Component_AbstractFactory {

    /**
     * Returns call stack instance.
     *
     * @return Performance_Profiler_Component_CallStack_Abstract
     */
    public function getCallStack() {
        if ($this->getAttemptId()) {
            $callStack = $this->getProvider()->get('Performance_Profiler_Component_CallStack_MySQL');
            $callStack->setAttemptId($this->getAttemptId());
        } else {
            $callStack = $this->getProvider()->get('Performance_Profiler_Component_CallStack_Default');
        }

        return $callStack;
    }
}
