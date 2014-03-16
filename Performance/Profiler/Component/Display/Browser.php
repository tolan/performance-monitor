<?php

namespace PF\Profiler\Component\Display;

use PF\Profiler\Enum\CallParameters;

/**
 * This script defines profiler display class for direct access from browser.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Browser extends AbstractDisplay {
    
    const CACHE_NAME = 'profiler_statistics';
    
    private $_flatStat = array();


    public function display() {
        $statistics = $this->getProvider()->get('PF\Profiler\Component\Statistics\Browser')->getStatistics();
        $cache      = $this->getProvider()->get('cache'); /* @var $cache \PF\Main\Cache */
        $this->_createFlatStatistics($statistics);
        
        $cache->has(self::CACHE_NAME) && $cache->clean(self::CACHE_NAME);
        $cache->save(self::CACHE_NAME, $this->_flatStat);
        
        echo $this->_createLink();
    }
    
    private function _createLink() {
        $config = $this->getProvider()->get('config')->get('profiler'); /* @var $config \PF\Main\Config */
        $link = $config['domain'].'/profiler/browser';
        $html = '<a href="'.$link.'" target="_blank" style="position: absolute; bottom: 10px; right: 10px;">Test</a>';
        
        return $html;
    }

    private function _createFlatStatistics(&$statistics, $parent = 0) {
        foreach ($statistics as &$call) {
            if (isset($call[CallParameters::SUB_STACK])) {
                $this->_createFlatStatistics($call[CallParameters::SUB_STACK], $call['id']);
                unset($call[CallParameters::SUB_STACK]);
            } else {
                $this->_flatStat[$parent][] = $call;
            }
        }
    }
}