<?php
/**
 * This file contains script for kill process by given process ID.
 * It is fired when application ends with memory leak.
 */

include __DIR__.'/../boot.php';

$pid              = $argv[1];
$i                = 0;
$cpuUsageTreshold = 5;
$process          = \PM\Main\Provider::getInstance()->get('PM\Main\System\Process'); /* @var $process \PM\Main\System\Process  */

while($i < 30) {
    $procUsage[$i] = $process->cpuUsage($pid);

    if ($procUsage[$i] < $cpuUsageTreshold && isset($procUsage[$i-1]) && $procUsage[$i-1] < $cpuUsageTreshold) {
        $process->exec('kill '.$pid);
        break;
    }

    sleep(1);
    $i++;
}