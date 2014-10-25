<?php

namespace PM\Profiler\Helper;

/**
 * TODO
 */
class Display
{
    /**
     * Core render method, which manipulate jquery and call render other data.
     *
     * @param array $statistics     Statistics data from performance profiler
     * @param array $additionalInfo Additional info array as time, memory, calls ...
     *
     * @return string $html HTML code
     */
    public static function render($statistics=null, $additionalInfo=null) {
        $html  = '<link rel="stylesheet" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />';
        $html .= '<script src="http://code.jquery.com/jquery-1.9.1.js"></script>';
        $html .= '<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>';
        $html .= '<script>';
        $html .= '$(function() {
            $(".accordion_main").accordion({
                collapsible: true,
                heightStyle: "content"
            });

            $(".accordion").accordion({
                collapsible: true,
                heightStyle: "content",
                active: false
            });
            $("#DEBUG_PROFILER").dialog({
                width: "80%",
                autoOpen: false,
                modal: true
            });
        });';
        $html .= 'function showDebug() {
                $("#DEBUG_PROFILER").dialog("open");
            }';
        $html .= '</script>';

        $html .= self::_renderButton($additionalInfo);

        $html .= '<div id="DEBUG_PROFILER" style="display: none" title="Debug profiler">';
        if (!is_null($additionalInfo)) {
            $html .= self::_renderAdditinalInfo($additionalInfo);
        }

        if (!is_null($statistics)) {
            $html .= self::_renderCalls($statistics);
        } else {
            $html .= '<div>Statistics were not obtained.</div>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render button with special information.
     *
     * @param array $additionalInfo Additional info array as time, memory, calls ...
     *
     * @return string
     */
    private static function _renderButton($additionalInfo=null) {
        $html = '<div class="button" style="position: fixed; bottom: 10px; right: 10px;">';
        if (is_null($additionalInfo)) {
            $html .= '<button onclick="showDebug()">DEBUG</button>';
        } else {
            $html .= '<button onclick="showDebug()">';
            $html .= '<span>Analyzing time: '.self::_formatTime($additionalInfo['analyzeTime']).'</span>';
            $html .= ' | <span>Memory by analyze: '.
                self::_formatToThousand($additionalInfo['analyzedMemory']).
                ' B</span>';
            $html .= ' | <span>Execution time: '.self::_formatTime($additionalInfo['time']).'</span>';
            $html .= ' | <span>Count of calls: '.self::_formatToThousand($additionalInfo['callsCount']).'</span>';
            $html .= ' | <span>Consumed memory: '.self::_formatToThousand($additionalInfo['memory']).' B</span>';
            $html .= ' | <span>Peak consumed memory: '.
                self::_formatToThousand($additionalInfo['memoryPeak']).
                ' B</span>';
            $html .= '</button>';
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Render method for additional info.
     *
     * @param array $additionalInfo Additional info array as time, memory, calls ...
     *
     * @return string
     */
    private static function _renderAdditinalInfo($additionalInfo) {
        $html  = '<div class="accordion_main">';
        $html .= '<h3><b>Main information</b></h3>';
        $html .= '<div>';
        $html .= '<table>';
        $html .= '<tr><td>Execution time</td><td>'.self::_formatTime($additionalInfo['time']).'</td></tr>';
        $html .= '<tr><td>Count of calls</td><td>'.self::_formatToThousand($additionalInfo['callsCount']).'</td></tr>';
        $html .= '<tr><td>Consumed memory</td><td>'.self::_formatToThousand($additionalInfo['memory']).' B</td></tr>';
        $html .= '<tr><td>Peak consumed memory</td><td>'.
            self::_formatToThousand($additionalInfo['memoryPeak']).
            ' B</td></tr>';
        $html .= '</table>';
        $html .= '</div>';
        $html .= '</div>';

        return $html;
    }

    /**
     * Render method for calls and subcalls.
     *
     * @param array $statistics Statistics data from performance profiler
     *
     * @return string
     */
    private static function _renderCalls($statistics) {
        $html = '<div class="accordion">';
        foreach ($statistics as $filename) {
            foreach ($filename as $line) {
                if ($line['time'] < 000) {
                    continue;
                }

                $html .= '<h3>'.
                    $line['lineContent'].
                    '<span style="float: right">'.
                    self::_formatTime($line['time']).
                    '</span></h3>';
                $html .= '<div>';
                $html .= '<table>';
                $html .= '<tr><td>Filename</td><td>'.$line['file'].'</td></tr>';
                $html .= '<tr><td>Line number</td><td>'.$line['line'].'</td></tr>';
                $html .= '<tr><td>Line content</td><td>'.$line['lineContent'].'</td></tr>';
                $html .= '<tr><td>Count of calls</td><td>'.$line['calls'].'</td></tr>';
                $html .= '<tr><td>Execution time</td><td>'.self::_formatTime($line['time']).'</td></tr>';
                $html .= '<tr><td>Max execution time</td><td>'.self::_formatTime($line['timeMax']).'</td></tr>';
                $html .= '<tr><td>Min execution time</td><td>'.self::_formatTime($line['timeMin']).'</td></tr>';
                $html .= '</table>';
                if (isset($line['statistics'])) {
                    $html .= '<div>';
                    $html .= self::_renderCalls($line['statistics']);
                    $html .= '</div>';
                }

                $html .= '</div>';
            }
        }

        $html .= '</div>';

        return $html;
    }

    /**
     * Time formater.
     *
     * @param float $time Time
     *
     * @return string
     */
    private static function _formatTime($time) {
        return number_format($time, 0, '.', ' ').' &micros';
    }

    /**
     * Format number to thousand.
     *
     * @param string $string String to format
     *
     * @return string
     */
    private static function _formatToThousand($string) {
        return number_format($string, 0, '.', ' ');
    }
}
