<?php
$config = array();

include __DIR__.'/Main/Provider.php';
include __DIR__.'/Main/Config.php';
include __DIR__.'/config.php';

$configInstance = new Performance_Main_Config();
$configInstance->fromArray($config);

$provider = Performance_Main_Provider::getInstance($configInstance);