<?php

namespace PF\Profiler\Component\CallStack;

use PF\Main\Provider;
use PF\Profiler\Enum\CallParameters;

/**
 * Abstract class for profiler call stack. Each call stck take all calls and transform it to tree.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractCallStack {

    /**
     * Provider instance
     *
     * @var \PF\Main\Provider
     */
    private $_provider;
    
    /**
     * Array with analyzed tree.
     *
     * @var array
     */
    protected $_analyzedTree = array();
    
    /**
     * Indicator for immersion level.
     *
     * @var int
     */
    protected $_actualLevel = 1;
    

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     *
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
        $this->init();
    }
    
    /**
     * This reset call stack to default values (erase analyzed tree, calls data and attempt information).
     *
     * @return \PF\Profiler\Component\CallStack\AbstractCallStack
     */
    public function reset() {
        $this->_actualLevel  = 1;
        $this->_analyzedTree = array();

        return $this;
    }
    
    /**
     * Returns analyzed tree.
     *
     * @return array Array with analyzed call stack tree
     */
    public function getAnalyzedTree() {
        $this->analyze();

        return $this->_analyzedTree;
    }
    
    /**
     * This create analyzed tree from calls.
     *
     * @return \PF\Profiler\Component\CallStack\MySQL
     */
    public function analyze() {
        if (empty($this->_analyzedTree)) {
            $data = $this->getStorageData();
            $this->_analyzedTree = $this->analyzeTree($data);
        }

        return $this;
    }
    
    /**
     * This analyze call stack tree from calls.
     *
     * @param array $stack Array with calls
     *
     * @return array
     */
    protected function analyzeTree(&$stack) {
        $result = array();

        while(!empty($stack)) {
            $call = array_shift($stack);

            if ($call[CallParameters::IMMERSION] == $this->_actualLevel) {
                $result[] = $call;
            } elseif ($call[CallParameters::IMMERSION] > $this->_actualLevel) {
                $this->_actualLevel++;
                array_unshift($stack, $call);
                $result[] = $this->analyzeTree($stack);
            } else {
                $this->_actualLevel--;
                $call[CallParameters::SUB_STACK] = $result;
                return $call;
            }
        }

        return $result;
    }
    
    /**
     * Optional init function instead of constructor.
     *
     * @return void
     */
    protected function init() {}
    
    /**
     * Returns array with calls.
     *
     * @return array
     */
    abstract protected function getStorageData();

    /**
     * Returns provider instance.
     *
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
}
