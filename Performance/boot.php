<?php

include __DIR__.'/Main/Provider.php';
include __DIR__.'/Main/Config.php';

$configInstance = new \PF\Main\Config();
$configInstance->loadJson(__DIR__.'/config.json');

$provider = \PF\Main\Provider::getInstance($configInstance);
