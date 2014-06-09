<?php

include __DIR__.'/../boot.php';

$provider = \PF\Main\Provider::getInstance();

try {
    $provider->get('web')->run();
} catch (\PF\Main\Exception $e) {
    $provider->get('log')->error($e->getMessage());
    $provider->get('log')->error($e->getTraceAsString());
}

