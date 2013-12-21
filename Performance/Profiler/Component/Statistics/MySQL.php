<?php

/**
 * This script defines profiler statistic class for access to MYSQL.
 *
 * @author     Martin Kovar
 * @category   Performance
 * @package    Profiler
 */
class Performance_Profiler_Component_Statistics_MySQL extends Performance_Profiler_Component_Statistics_Abstract {

    /**
     * ID of attempt.
     *
     * @var int
     */
    private $_attemptId;

    /**
     * Compensation time.
     *
     * @var float
     */
    private $_compTime = 0;

    /**
     * Patterns for resolve cycles.
     *
     * @var array
     */
    private static $_cyclesPaterns = array(
        'while ?\(', 'for ?\(', 'foreach ?\(', 'do ?\{'
    );

    /**
     * Repository for measure statistic.
     *
     * @var Performance_Profiler_Component_Repository_TestAttempt
     */
    private $_attemptRepository     = null;

    /**
     * Repository for measure statistic data.
     *
     * @var Performance_Profiler_Component_Repository_AttemptStatisticData
     */
    private $_statDataRepository = null;

    /**
     * Analyzed call stack tree.
     *
     * @var array
     */
    private $_callStack = null;

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
     * Counter for calls.
     *
     * @var int
     */
    private $_countCalls = 0;

    /**
     * Storage for content of files.
     *
     * @var array
     */
    private static $_filesCache = array();

    /**
     * Method for reset variable to defaults (reset atempt ID, statistics, file cache, times, count of calls).
     *
     * @return void
     */
    public function reset() {
        $this->_attemptId  = null;
        $this->_statistics = array();
        $this->_times      = array();
        $this->_countCalls = 0;
        self::$_filesCache = array();
    }

    /**
     * Sets ID of attempt.
     *
     * @param int $id ID of attempt
     *
     * @return Performance_Profiler_Component_Statistics_MySQL
     */
    public function setAttemptId($id) {
        $this->_attemptId = $id;

        $attempt = $this->_attemptRepository->getAttempt($id);
        $this->_compTime = $attempt['compensationTime'];

        return $this;
    }

    /**
     * Generate statistics from analyzed tree.
     *
     * @return Performance_Profiler_Component_Statistics_MySQL
     */
    public function generate() {
        if (empty($this->_statistics)) {
            $this->_times[0] = 0;
            $this->_statistics = $this->_generate($this->_callStack->getAnalyzedTree());
        }

        return $this;
    }

    /**
     * Save statistics to MYSQL.
     *
     * @return Performance_Profiler_Component_Statistics_MySQL
     */
    public function save() {
        $this->generate();
        $this->_save($this->_statistics, $this->_attemptId);
        $updateData = array(
            'time' => $this->_times[0],
            'calls' => $this->_countCalls
        );
        $this->_attemptRepository->update($this->_attemptId, $updateData);

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
            $time = 0;
            if (isset($call['subStack'])) {
                $this->_times[$call['id']] = 0;
                $this->_generate($call['subStack'], $call['id']);
                $time = $this->_times[$call['id']];
                unset($this->_times[$call['id']]);
            }

            $answer = $this->_getContent($call['file'], $call['line']);
            $call['time']    = $this->_getTime($call) + $time;
            $call['content'] = $answer['content'];
            $call['lines']   = $answer['lines'];
            $this->_times[$parent] += $call['time'];
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
            if (!isset($call['subStack']) && preg_match('#('.join('|', self::$_cyclesPaterns).')#', $call['content'])) {
                if (isset($tree[$key-1]) && $call['line'] === $tree[$key-1]['line']) {
                    unset ($tree[$key]);
                    continue;
                } elseif($call['lines'] > 1) {
                    $this->_handleCycleSubStack($tree, $call, $key);
                    $call['time'] += isset($call['subStack']) ? $this->_getTimeSubStack($call['subStack']) : 0;
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
     * @return Performance_Profiler_Component_Statistics_MySQL
     */
    private function _handleCycleSubStack(&$tree, &$cycleCall, $key) {
        $startLine = $cycleCall['line'] - ($cycleCall['lines'] - 1);
        $endLine   = $cycleCall['line'];
        $subStack  = array();
        $lines     = array();

        for ($i = 0; $i < $key; $i++) {
            if (isset($tree[$i])) {
                $call = $tree[$i];
                if ($call['line'] > $startLine && $call['line'] < $endLine && !in_array($call['line'], $lines)) {
                    $lines[] = $call['line'];
                    array_push($subStack, $call);
                    unset($tree[$i]);
                }
            }
        }

        if (count($subStack) > 0) {
            $cycleCall['subStack'] = $subStack;
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
            $time += $subCall['time'];
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
        return max((($call['end'] - $call['start']) - $this->_compTime), 0);
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

        $result = trim($file[$line - 1]);
        $countOfLines = 1;

        while(!$this->_checkCompleteContent($result) && $line > 1) {
            $line--;
            $countOfLines++;
            $result = trim($file[$line - 1]).' '.$result;
        }

        return array('content' => $result, 'lines' => $countOfLines);
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
        $pairs = array(
            array('{', '}'),
            array('(', ')'),
            array('[', ']'),
            array('"', '"'),
            array("'", "'")
        );

        foreach ($pairs as $pair) {
            if (substr_count($content, $pair[0]) !== substr_count($content, $pair[1])) {
                return false;
            }

            if (strpos($content, $pair[0]) === 0) {
                return false;
            }
        }

        return true;
    }

    /**
     * It saves statistics data to MYSQL.
     *
     * @param array $statistics Analyzed trre with statistics
     * @param int   $attemptId  ID of attempt
     * @param int   $parent     ID of statistic data parent id (0 is root)
     *
     * @return Performance_Profiler_Component_Statistics_MySQL
     */
    private function _save(&$statistics, $attemptId = null, $parent = 0) {
        $this->_times[$parent] = 0;
        $respository = $this->_statDataRepository;

        foreach ($statistics as &$call) {
            $this->_times[$parent] += $call['time'];
            $this->_countCalls++;
            $data = array(
                'attemptId' => $attemptId,
                'parentId'  => $parent,
                'file'      => $call['file'],
                'line'      => $call['line'],
                'content'   => $call['content'],
                'time'      => $call['time']
            );
            $id = $respository->create($data);
            if (isset($call['subStack'])) {
                $this->_save($call['subStack'], $attemptId, $id);
            }
        }

        return $this;
    }

    /**
     * Init method for set call stack and repositories.
     *
     * @return void
     */
    protected function init() {
        $this->_callStack          = $this->getProvider()
                ->get('Performance_Profiler_Component_CallStack_Factory')->getCallStack(); /* @var $callStack Performance_Profiler_Component_CallStack_MySQL */
        $this->_attemptRepository  = $this->getProvider()->get('Performance_Profiler_Component_Repository_TestAttempt');
        $this->_statDataRepository = $this->getProvider()->get('Performance_Profiler_Component_Repository_AttemptStatisticData');
    }
}