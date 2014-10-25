<?php

namespace PM\Profiler\Monitor;

/**
 * This script defines class for monitor analyzator. The analyzator analyze list of calls and transfor, it into call stack tree.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Analyzator implements Interfaces\Analyzator {

    /**
     * Monitor storage instance
     *
     * @var \PM\Profiler\Monitor\Interfaces\Storage
     */
    private $_storage;

    /**
     * Pointer for actual level of immersion
     *
     * @var int
     */
    private $_actualLevel = 1;

    /**
     * Construct method.
     *
     * @param \PM\Profiler\Monitor\Interfaces\Storage $storage Monitor storage instance
     *
     * @return void
     */
    public function __construct(Interfaces\Storage $storage) {
        $this->_storage = $storage;
    }

    /**
     * It provides analzing list of calls stored in storage. It take list of calls and transform it into call stack tree.
     *
     * @return \PM\Profiler\Monitor\Analyzator
     */
    public function analyze() {
        if ($this->_storage->getState() !== Storage\State::STATE_ANALYZED) {
            $this->_storage->setState(Storage\State::STATE_ANALYZING);
            $this->_storage->rewind();

            $tree = $this->_analyzeTree($this->_storage);
            reset($tree);
            $analyzed = $this->_checkStatement($tree);
            $this->_storage->fromArray($analyzed);

            $this->_storage->setState(Storage\State::STATE_ANALYZED);
        }

        return $this;
    }

    /**
     * Process of analyze list of calls to call stack.
     *
     * @param \PM\Profiler\Monitor\Storage $storage Monitor storage instance
     *
     * @return array
     */
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
            } else if ($this->_actualLevel == ($call[Enum\CallAttributes::IMMERSION] + 1)) {
                $this->_actualLevel--;
                foreach ($result as $item) {
                    if (isset($item[Enum\CallAttributes::SUB_STACK]) && !isset($item[Enum\CallAttributes::FILE])) {
                        $result = $item[Enum\CallAttributes::SUB_STACK];
                        break;
                    }
                }

                reset($result);
                $call[Enum\CallAttributes::SUB_STACK] = $result;
                $result = $call;
                unset($storage[$key]);
                break;
            } else { // actual level is higher than immersion of call by 2 or more steps
                $this->_actualLevel--;
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
    private function _checkStatement(&$tree) {
        while(current($tree)) {
            $key  = key($tree);
            $call = &$tree[$key];

            if (!isset($call[Enum\CallAttributes::SUB_STACK])) {
                if (isset($tree[$key - 1]) && $call[Enum\CallAttributes::LINE] === $tree[$key - 1][Enum\CallAttributes::LINE] &&
                    $this->_storage->getCallInstance()->isStatement($call)) {
                    unset ($tree[$key]); // unsets last call of cycle because it is end of cycle without throughput
                } elseif($call[Enum\CallAttributes::LINES] > 1 && $this->_storage->getCallInstance()->isStatement($call)) {
                    $this->_handleStatementSubStack($tree, $call, $key);
                }
            } else {
                $this->_checkStatement($call[Enum\CallAttributes::SUB_STACK]);
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
     * @return \PM\Profiler\Monitor\Analyzator
     */
    private function _handleStatementSubStack(&$tree, &$cycleCall, $key) {
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
