<?php

include __DIR__.'/../boot.php';

$provider = \PM\Main\Provider::getInstance();

try {
    $provider->get('web')->run();
} catch (\PM\Main\Exception $e) {
    $provider->get('log')->error($e->getMessage());
    $provider->get('log')->error($e->getTraceAsString());
}