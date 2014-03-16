<?php

namespace PF\Profiler\Main;

class Statistic implements Interfaces\Statistic {

    private $_storage;
    private $_compensationTime = 0;

    public function __construct(Interfaces\Storage $storage) {
        $this->_storage = $storage;
    }

    public function generate() {
        if ($this->_storage->getState() !== Storage\State::STATE_STAT_GENERATED) {
            $this->_storage->setState(Storage\State::STATE_STAT_GENERATING);
            $this->_storage->rewind();
            $processed = $this->_generate($this->_storage);
            $this->_storage->fromArray($processed);
            $this->_storage->setState(Storage\State::STATE_STAT_GENERATED);
        }

        return $this;
    }

    private function _generate(Storage $storage) {
        $result = array();
        while($storage->valid()) {
            $call = $storage->arrayShift();

            $this->_generateCall($call);

            $result[] = $call;
        }

        return $result;
    }

    private function _generateSubTree(&$tree) {
        foreach ($tree as &$call) {
            $this->_generateCall($call);
        }
    }

    private function _generateCall(&$call) {
        $call[Enum\CallAttributes::TIME] = $this->_getTime($call);

        if (isset($call[Enum\CallAttributes::SUB_STACK])) {
            $this->_generateSubTree($call[Enum\CallAttributes::SUB_STACK]);
            $time = $this->_getTimeSubStack($call[Enum\CallAttributes::SUB_STACK]);
            $call[Enum\CallAttributes::TIME_SUB_STACK] = $call[Enum\CallAttributes::TIME] + $time;
        }
    }

    /**
     * It compute new time of sub-stack.
     *
     * @param array $subStack Array with calls in sub-stack
     *
     * @return float
     */
    private function _getTimeSubStack(&$subStack) {
        $time = 0;
        foreach($subStack as &$subCall) {
            if (isset($subCall[Enum\CallAttributes::TIME_SUB_STACK])) {
                $time += $subCall[Enum\CallAttributes::TIME_SUB_STACK];
            } else {
                $time += $subCall[Enum\CallAttributes::TIME];
            }
        }

        return $time;
    }

    /**
     * Return compensated time of call.
     *
     * @param array $call Array with call
     *
     * @return float
     */
    private function _getTime(&$call) {
        $start = isset($call[Enum\CallAttributes::START_TIME]) ? $call[Enum\CallAttributes::START_TIME] : 0;
        $end   = isset($call[Enum\CallAttributes::END_TIME])   ? $call[Enum\CallAttributes::END_TIME]   : 0;

        return max((($end - $start) - $this->_compensationTime), 0);
    }
}
