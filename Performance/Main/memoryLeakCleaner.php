<?php

include __DIR__.'/../boot.php';

$pid              = $argv[1];
$i                = 0;
$cpuUsageTreshold = 5;
$process          = \PF\Main\Provider::getInstance()->get('PF\Main\System\Process'); /* @var $process \PF\Main\System\Process  */

while($i < 30) {
    $procUsage[$i] = $process->cpuUsage($pid);

    if ($procUsage[$i] < $cpuUsageTreshold && isset($procUsage[$i-1]) && $procUsage[$i-1] < $cpuUsageTreshold) {
        $process->exec('kill '.$pid);
        break;
    }

    sleep(1);
    $i++;
}