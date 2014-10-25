<?php

include __DIR__.'/Main/Provider.php';
include __DIR__.'/Main/Config.php';

$configInstance = \PM\Main\Config::getInstance();
$configInstance->loadJson(__DIR__.'/config.json');

$provider = \PM\Main\Provider::getInstance($configInstance);