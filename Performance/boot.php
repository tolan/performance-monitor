<?php

include __DIR__.'/Main/Provider.php';
include __DIR__.'/Main/Config.php';

$configInstance = new Performance_Main_Config();
$configInstance->loadJson(__DIR__.'/config.json');

$provider = Performance_Main_Provider::getInstance($configInstance);