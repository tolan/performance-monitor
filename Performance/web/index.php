<?php

include __DIR__.'/../boot.php';

$provider = \PF\Main\Provider::getInstance();

try {
    $provider->get('web')->run();
} catch (\PF\Main\Exception $e) {
error_log(date("Y-m-d H:i:s")."#".__FILE__.":".__LINE__."\r\n".print_r($e->getTraceAsString(),TRUE)."\r\n\n\n", 3, "/home/tolan/my.log");
    $provider->get('log')->error($e->getMessage());
    $provider->get('log')->error($e->getTraceAsString());
}

