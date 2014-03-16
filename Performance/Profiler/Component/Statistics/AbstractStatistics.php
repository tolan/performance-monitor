<?php

namespace PF\Profiler\Component\Statistics;

use PF\Main\Provider;
use PF\Profiler\Enum\CallParameters;

/**
 * Abstract class for profiler statistics.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
abstract class AbstractStatistics {

    /**
     * Provider instance
     *
     * @var \PF\Main\Provider
     */
    private $_provider;
    
    /**
     * Analyzed call stack tree with statistics data.
     *
     * @var array
     */
    private $_statistics = array();

    /**
     * Helper storage variable for compute times for each call.
     *
     * @var array
     */
    private $_times = array();
    
    /**
     * Storage for content of files.
     *
     * @var array
     */
    private static $_filesCache = array();
    
    /**
     * Compensation time.
     *
     * @var float
     */
    private $_compensationTime = 0;
    
    /**
     * Patterns for resolve cycles.
     *
     * @var array
     */
    private static $_cyclesPaterns = array(
        'while ?\(', 'for ?\(', 'foreach ?\(', 'do ?\{'
    );

    /**
     * Construct method.
     *
     * @param \PF\Main\Provider $provider Provider instance
     */
    final public function __construct(Provider $provider) {
        $this->_provider = $provider;
        $this->init();
    }

    /**
     * Generate statistics from analyzed tree.
     *
     * @return \PF\Profiler\Component\Statistics\MySQL
     */
    public function generate() {
        if (empty($this->_statistics)) {
            $this->_times      = array(0 => 0);
            $this->_statistics = $this->_generate($this->getAnalyzedTree());
        }

        return $this;
    }

    public function reset() {
        $this->_statistics = array();
        $this->_times      = array();
        self::$_filesCache = array();

        return $this;
    }

    public function getStatistics() {
        $this->generate();


        return $this->_statistics;
    }

    /**
     * Optional init method instead of construct.
     *
     * @return void
     */
    protected function init() {}

    /**
     * Gets provider instance.
     *
     * @return \PF\Main\Provider
     */
    final protected function getProvider() {
        return $this->_provider;
    }
    
    abstract protected function getAnalyzedTree();
    
    protected function getTimes() {
        return $this->_times;
    }
    
    protected function setCompensationTime($time = 0) {
        $this->_compensationTime = $time;
        
        return $this;
    }

    /**
     * Generates statistics for each call in analyzed tree.
     *
     * @param array $tree   Analyzed tree
     * @param int   $parent ID of parent
     *
     * @return array Array with analyzed tree with statistics
     */
    private function _generate(&$tree, $parent = 0) {
        foreach ($tree as &$call) {
            $call['id'] = isset($call['id']) ? $call['id'] : uniqid();
            $time       = 0;

            if (isset($call[CallParameters::SUB_STACK])) {
                $this->_times[$call['id']] = 0;
                $this->_generate($call[CallParameters::SUB_STACK], $call['id']);
                $time = $this->_times[$call['id']];
                unset($this->_times[$call['id']]);
            }

            $answer = $this->_getContent($call[CallParameters::FILE], $call[CallParameters::LINE]);
            $call[CallParameters::TIME]           = $this->_getTime($call);
            $call[CallParameters::TIME_SUB_STACK] = $call[CallParameters::TIME] + $time;
            $call[CallParameters::CONTENT]        = $answer[CallParameters::CONTENT];
            $call[CallParameters::LINES]          = $answer[CallParameters::LINES];
            $this->_times[$parent]               += $call[CallParameters::TIME_SUB_STACK];
        }

        $this->_checkCycle($tree);

        return $tree;
    }

    /**
     * This checks cycles and transform it to substack.
     *
     * @param array $tree Analyzed tree
     *
     * @return void
     */
    private function _checkCycle(&$tree) {
        foreach ($tree as $key => &$call) {
            if (!isset($call[CallParameters::SUB_STACK]) && preg_match('#('.join('|', self::$_cyclesPaterns).')#', $call[CallParameters::CONTENT])) {
                if (isset($tree[$key-1]) && $call[CallParameters::LINE] === $tree[$key-1][CallParameters::LINE]) {
                    unset ($tree[$key]);
                    continue;
                } elseif($call[CallParameters::LINES] > 1) {
                    $this->_handleCycleSubStack($tree, $call, $key);
                    $call[CallParameters::TIME_SUB_STACK] += isset($call[CallParameters::SUB_STACK]) ? $this->_getTimeSubStack($call[CallParameters::SUB_STACK]) : 0;
                }
            }
        }
    }

    /**
     * Handle cycle sub-stack. It extracts calls between lines of call and move it to sub-stack of call.
     *
     * @param array $tree      Analyzed tree
     * @param array $cycleCall Call with cycle
     * @param int   $key       Key of call in tree
     *
     * @return \PF\Profiler\Component\Statistics\MySQL
     */
    private function _handleCycleSubStack(&$tree, &$cycleCall, $key) {
        $startLine = $cycleCall[CallParameters::LINE] - ($cycleCall[CallParameters::LINES] - 1);
        $endLine   = $cycleCall[CallParameters::LINE];
        $subStack  = array();
        $lines     = array();

        for ($i = 0; $i < $key; $i++) {
            if (isset($tree[$i])) {
                $call = $tree[$i];
                if ($call[CallParameters::LINE] > $startLine && $call[CallParameters::LINE] < $endLine && !in_array($call[CallParameters::LINE], $lines)) {
                    $lines[] = $call[CallParameters::LINE];
                    array_push($subStack, $call);
                    unset($tree[$i]);
                }
            }
        }

        if (count($subStack) > 0) {
            $cycleCall[CallParameters::SUB_STACK] = $subStack;
        }

        return $this;
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
            $time += $subCall[CallParameters::TIME] + $subCall[CallParameters::TIME_SUB_STACK];
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
        return max((($call['end'] - $call['start']) - $this->_compensationTime), 0);
    }

    /**
     * Returns content of line in file.
     *
     * @param string $filename Path to file
     * @param int    $line     Line of file
     *
     * @return string Content of line in file
     */
    private function _getContent($filename, $line) {
        if (!isset(self::$_filesCache[$filename])) {
            self::$_filesCache[$filename] = file($filename);
        }

        $file = self::$_filesCache[$filename];

        $result       = trim($file[$line - 1]);
        $countOfLines = 1;

        while(!$this->_checkCompleteContent($result) && $line > 1) {
            $line--;
            $countOfLines++;
            $result = trim($file[$line - 1]).' '.$result;
        }

        return array(CallParameters::CONTENT => $result, CallParameters::LINES => $countOfLines);
    }

    /**
     * This checks whether content of line is complete.
     *
     * @param string $content Content of line
     *
     * @return boolean
     */
    private function _checkCompleteContent($content) {
        $content = trim($content);
        $result  = true;
        $pairs   = array(
            array('{', '}'),
            array('(', ')'),
            array('[', ']'),
            array('"', '"'),
            array("'", "'")
        );

        foreach ($pairs as $pair) {
            if (substr_count($content, $pair[0]) !== substr_count($content, $pair[1])) {
                $result = false;
                break;
            }

            if (strpos($content, $pair[0]) === 0) {
                $result = false;
                break;
            }
        }

        return $result;
    }
}
