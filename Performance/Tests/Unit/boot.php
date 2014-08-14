<?php

include __DIR__.'/../../Main/Provider.php';
include __DIR__.'/../../Main/Config.php';

$configInstance = \PF\Main\Config::getInstance();
$configInstance->loadJson(__DIR__.'/config.json');

$provider = \PF\Main\Provider::getInstance($configInstance);