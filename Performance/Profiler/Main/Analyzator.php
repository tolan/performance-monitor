<?php

namespace PF\Profiler\Main;

class Analyzator implements Interfaces\Analyzator {

    private $_storage;

    private $_actualLevel = 1;

    public function __construct(Interfaces\Storage $storage) {
        $this->_storage = $storage;
    }

    public function analyze() {
        if ($this->_storage->getState() !== Storage\State::STATE_ANALYZED) {
            $this->_storage->setState(Storage\State::STATE_ANALYZING);
            $this->_storage->rewind();

            $tree = $this->_analyzeTree($this->_storage);
            reset($tree);
            $analyzed = $this->_checkCycle($tree);
            $this->_storage->fromArray($analyzed);

            $this->_storage->setState(Storage\State::STATE_ANALYZED);
        }

        return $this;
    }

    private function _analyzeTree(Storage $storage) {
        $result = array();

        while($storage->valid()) {
            $key     = $storage->key();
            $call    = $storage->current();
            $content = $this->_storage->getCallInstance()->getContent($call);

            $call[Enum\CallAttributes::CONTENT] = $content[Enum\CallAttributes::CONTENT];
            $call[Enum\CallAttributes::LINES]   = $content[Enum\CallAttributes::LINES];

            if ($call[Enum\CallAttributes::IMMERSION] == $this->_actualLevel) {
                $result[] = $call;
                unset($storage[$key]);
            } elseif ($call[Enum\CallAttributes::IMMERSION] > $this->_actualLevel) {
                $this->_actualLevel++;
                $analyzed = $this->_analyzeTree($storage);
                $result[] = isset($analyzed[Enum\CallAttributes::SUB_STACK]) ? $analyzed : array(Enum\CallAttributes::SUB_STACK => $analyzed);
            } else {
                $this->_actualLevel--;
                $call[Enum\CallAttributes::SUB_STACK] = $result;
                $result = $call;
                unset($storage[$key]);
                break;
            }
        }

        return $result;
    }

    /**
     * This checks cycles and transform it to substack.
     *
     * @param array $tree Analyzed tree
     *
     * @return void
     */
    private function _checkCycle(&$tree) {
        while(current($tree)) {
            $key  = key($tree);
            $call = &$tree[$key];

            if (!isset($call[Enum\CallAttributes::SUB_STACK])) {
                if (isset($tree[$key-1]) && $call[Enum\CallAttributes::LINE] === $tree[$key-1][Enum\CallAttributes::LINE] &&
                    $this->_storage->getCallInstance()->isCycle($call)) {
                    unset ($tree[$key]); // unsets last call of cycle because it is end of cycle without throughput
                } elseif($call[Enum\CallAttributes::LINES] > 1 && $this->_storage->getCallInstance()->isCycle($call)) {
                    $this->_handleCycleSubStack($tree, $call, $key);
                }
            } else {
                $this->_checkCycle($call[Enum\CallAttributes::SUB_STACK]);
            }

            next($tree);
        }

        return $tree;
    }

    /**
     * Handle cycle sub-stack. It extracts calls between lines of call and move it to sub-stack of call.
     *
     * @param array $tree      Analyzed tree
     * @param array $cycleCall Call with cycle
     * @param int   $key       Key of call in tree
     *
     * @return \PF\Profiler\Main\Analyzator
     */
    private function _handleCycleSubStack(&$tree, &$cycleCall, $key) {
        $startLine = $cycleCall[Enum\CallAttributes::LINE] - ($cycleCall[Enum\CallAttributes::LINES] - 1);
        $endLine   = $cycleCall[Enum\CallAttributes::LINE];
        $startKey  = max ($key - ($cycleCall[Enum\CallAttributes::LINES]), 0);
        $lines     = array();

        for ($i = $startKey; $i < $key; $i++) {
            if (isset($tree[$i])) {
                $call = &$tree[$i];
                if ($call[Enum\CallAttributes::LINE] > $startLine &&
                    $call[Enum\CallAttributes::LINE] < $endLine &&
                    !array_key_exists($call[Enum\CallAttributes::LINE], $lines)
                ) {
                    $lines[$call[Enum\CallAttributes::LINE]]     = $call[Enum\CallAttributes::LINE];
                    $cycleCall[Enum\CallAttributes::SUB_STACK][] = $call;
                    unset($tree[$i]);
                }
            }
        }

        return $cycleCall;
    }
}
