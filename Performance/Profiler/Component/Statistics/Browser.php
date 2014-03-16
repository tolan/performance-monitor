<?php

namespace PF\Profiler\Component\Statistics;

/**
 * This script defines profiler statistic class for direct access from browser.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Browser extends AbstractStatistics {
    
    /**
     * Call stack instance
     * 
     * @var \PF\Profiler\Component\CallStack\Browser 
     */
    private $_callStack = null;


    protected function init() {
        $this->_callStack = $this->getProvider()->get('PF\Profiler\Component\CallStack\Browser'); 
    }

    protected function getAnalyzedTree() {
        return $this->_callStack->getAnalyzedTree();
    }
}