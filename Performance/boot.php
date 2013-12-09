<?php
session_start();
$config = array();

include $_SERVER['DOCUMENT_ROOT'].'/Performance/Main/Provider.php';
include $_SERVER['DOCUMENT_ROOT'].'/Performance/Main/Config.php';
include $_SERVER['DOCUMENT_ROOT'].'/Performance/config.php';

$configInstance = new Performance_Main_Config();
$configInstance->fromArray($config);

Performance_Main_Provider::getInstance($configInstance);