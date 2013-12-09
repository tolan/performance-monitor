<?php

// @codingStandardsIgnoreStart
include $_SERVER['DOCUMENT_ROOT'].'/Performance/boot.php';
// @codingStandardsIgnoreEnd
try {
    Performance_Main_Provider::getInstance()->get('web')->run();
} catch (Performance_Main_Exception $e) {
    error_log(date("Y-m-d H:i:s")."#".__FILE__.":".__LINE__."\r\n".print_r($e->getMessage(),TRUE)."\r\n\n\n", 3, "/home/tolan/my.log");
    error_log(date("Y-m-d H:i:s")."#".__FILE__.":".__LINE__."\r\n".print_r($e->getTraceAsString(),TRUE)."\r\n\n\n", 3, "/home/tolan/my.log");
}

